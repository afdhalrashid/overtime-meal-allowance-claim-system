<?php

namespace App\Livewire\Claims;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ClaimForm extends Component
{
    use WithFileUploads;

    public $claimID;
    public $claim;
    public $isEditing = false;

    // Form fields
    public $duty_date;
    public $start_time;
    public $end_time;
    public $work_type = 'in_office';
    public $travel_start_time;
    public $travel_end_time;
    public $travel_origin;
    public $travel_destination;
    public $travel_purpose;
    public $remarks;

    // File uploads
    public $attendance_record;
    public $supporting_documents = [];

    // Calculated fields
    public $overtime_hours = 0;
    public $meal_allowance_amount = 0;
    public $total_amount = 0;

    protected $rules = [
        'duty_date' => 'required|date|before_or_equal:today',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'work_type' => 'required|in:in_office,out_of_office',
        'travel_start_time' => 'nullable|required_if:work_type,out_of_office',
        'travel_end_time' => 'nullable|required_if:work_type,out_of_office|after:travel_start_time',
        'travel_origin' => 'nullable|required_if:work_type,out_of_office|string|max:255',
        'travel_destination' => 'nullable|required_if:work_type,out_of_office|string|max:255',
        'travel_purpose' => 'nullable|required_if:work_type,out_of_office|string|max:500',
        'remarks' => 'nullable|string|max:1000',
    ];

    protected function getValidationRules()
    {
        $rules = $this->rules;

        // Document validation rules
        $hasAttendanceRecord = $this->isEditing && $this->claim && $this->claim->attendanceRecordDocument;
        $hasSupportingDocs = $this->isEditing && $this->claim && $this->claim->supportingDocumentsCollection->count() > 0;

        // Only require attendance record if editing without existing document or creating new
        if (!$hasAttendanceRecord) {
            $rules['attendance_record'] = 'required|file|max:5120|mimes:pdf,jpg,jpeg,png,docx';
        } else {
            $rules['attendance_record'] = 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,docx';
        }

        // Only require supporting documents if editing without existing documents or creating new
        if (!$hasSupportingDocs) {
            $rules['supporting_documents.*'] = 'required|file|max:5120|mimes:pdf,jpg,jpeg,png,docx';
        } else {
            $rules['supporting_documents.*'] = 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,docx';
        }

        return $rules;
    }

    public function mount($claim = null)
    {
        if ($claim) {
            $this->claimID = $claim;
            $this->isEditing = true;
            $this->fillForm();
        } else {
            $this->duty_date = now()->format('Y-m-d');
        }
    }

    private function fillForm()
    {
        $this->claim = Claim::find($this->claimID);
        $this->duty_date = $this->claim->duty_date->format('Y-m-d');
        $this->start_time = $this->claim->start_time->format('H:i');
        $this->end_time = $this->claim->end_time->format('H:i');
        $this->work_type = $this->claim->work_type;
        $this->travel_start_time = $this->claim->travel_start_time?->format('H:i');
        $this->travel_end_time = $this->claim->travel_end_time?->format('H:i');
        $this->travel_origin = $this->claim->travel_origin;
        $this->travel_destination = $this->claim->travel_destination;
        $this->travel_purpose = $this->claim->travel_purpose;
        $this->remarks = $this->claim->remarks;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['duty_date', 'start_time', 'end_time', 'work_type', 'travel_start_time', 'travel_end_time'])) {
            $this->calculateAmounts();
        }
    }

    private function calculateAmounts()
    {
        if (!$this->duty_date || !$this->start_time || !$this->end_time) {
            return;
        }

        $dutyDate = Carbon::parse($this->duty_date);
        $startTime = Carbon::parse($this->duty_date . ' ' . $this->start_time);
        $endTime = Carbon::parse($this->duty_date . ' ' . $this->end_time);

        // Handle overnight work
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }

        $workHours = $endTime->diffInHours($startTime, true);

        // Subtract travel time if applicable
        if ($this->work_type === 'out_of_office' && $this->travel_start_time && $this->travel_end_time) {
            $travelStart = Carbon::parse($this->duty_date . ' ' . $this->travel_start_time);
            $travelEnd = Carbon::parse($this->duty_date . ' ' . $this->travel_end_time);

            if ($travelEnd->lt($travelStart)) {
                $travelEnd->addDay();
            }

            $travelHours = $travelEnd->diffInHours($travelStart, true);

            // Only subtract travel time if user doesn't involve driving
            if (!auth()->user()->involves_driving) {
                $workHours -= $travelHours;
            }
        }

        // Calculate overtime hours
        $standardHours = 9; // Standard working hours per day
        $this->overtime_hours = max(0, $workHours - $standardHours);

        // Calculate meal allowance
        $this->meal_allowance_amount = 0;
        if ($this->overtime_hours >= 2 && $endTime->hour >= 19) {
            $this->meal_allowance_amount = SystemSetting::get('meal_allowance_amount', 15);
        }

        // Calculate overtime amount based on day type
        $overtimeRate = $this->getOvertimeRate($dutyDate);
        $overtimeAmount = $this->overtime_hours * $overtimeRate;

        $this->total_amount = $overtimeAmount + $this->meal_allowance_amount;
    }

    private function getOvertimeRate(Carbon $date): float
    {
        // Check if it's a public holiday
        if (\App\Models\PublicHoliday::isPublicHoliday($date)) {
            return SystemSetting::get('overtime_rate_holiday', 50);
        }

        // Check if it's a weekend
        if ($date->isWeekend()) {
            return SystemSetting::get('overtime_rate_weekend', 35);
        }

        // Weekday rate
        return SystemSetting::get('overtime_rate_weekday', 25);
    }

    public function save()
    {
        $this->validate($this->getValidationRules());

        // Check submission deadline
        $dutyDate = Carbon::parse($this->duty_date);
        $deadline = $dutyDate->copy()->addMonths(2);

        if (now()->isAfter($deadline)) {
            session()->flash('error', 'Cannot submit claim. Submission deadline has passed.');
            return;
        }

        try {
            $claimData = [
                'user_id' => auth()->id(),
                'duty_date' => $this->duty_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'work_type' => $this->work_type,
                'travel_start_time' => $this->work_type === 'out_of_office' ? $this->travel_start_time : null,
                'travel_end_time' => $this->work_type === 'out_of_office' ? $this->travel_end_time : null,
                'travel_origin' => $this->work_type === 'out_of_office' ? $this->travel_origin : null,
                'travel_destination' => $this->work_type === 'out_of_office' ? $this->travel_destination : null,
                'travel_purpose' => $this->work_type === 'out_of_office' ? $this->travel_purpose : null,
                'overtime_hours' => $this->overtime_hours,
                'meal_allowance_amount' => $this->meal_allowance_amount,
                'total_amount' => $this->total_amount,
                'has_overtime' => $this->overtime_hours > 0,
                'has_meal_allowance' => $this->meal_allowance_amount > 0,
                'remarks' => $this->remarks,
                'status' => 'draft',
            ];

            if ($this->isEditing) {
                $this->claim->update($claimData);
            } else {
                $claimData['claim_number'] = Claim::generateClaimNumber();
                $this->claim = Claim::create($claimData);
            }

            // Handle file uploads
            $this->handleFileUploads();

            session()->flash('success', 'Claim saved successfully!');

            return redirect()->route('staff.dashboard');

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving claim: ' . $e->getMessage());
        }
    }

    private function handleFileUploads()
    {
        // Handle attendance record
        if ($this->attendance_record) {
            $this->uploadDocument($this->attendance_record, 'attendance_record');
        }

        // Handle supporting documents
        foreach ($this->supporting_documents as $document) {
            if ($document) {
                $this->uploadDocument($document, 'supporting_document');
            }
        }
    }

    private function uploadDocument($file, $type)
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('claim-documents/' . $this->claim->id, $filename, 'local');

        ClaimDocument::create([
            'claim_id' => $this->claim->id,
            'document_type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $filename,
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    public function submit()
    {
        if (!$this->claim || !$this->claim->hasRequiredDocuments()) {
            session()->flash('error', 'Please upload all required documents before submitting.');
            return;
        }

        $this->claim->update([
            'status' => 'pending_approval',
            'submitted_at' => now()
        ]);

        // TODO: Send notification to manager

        session()->flash('success', 'Claim submitted for approval!');
        return redirect()->route('staff.dashboard');
    }

    public function submitForApproval()
    {
        $this->validate($this->getValidationRules());

        // Check submission deadline
        $dutyDate = Carbon::parse($this->duty_date);
        $deadline = $dutyDate->copy()->addMonths(2);

        if (now()->isAfter($deadline)) {
            session()->flash('error', 'Cannot submit claim. Submission deadline has passed.');
            return;
        }

        try {
            $claimData = [
                'user_id' => auth()->id(),
                'duty_date' => $this->duty_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'work_type' => $this->work_type,
                'travel_start_time' => $this->work_type === 'out_of_office' ? $this->travel_start_time : null,
                'travel_end_time' => $this->work_type === 'out_of_office' ? $this->travel_end_time : null,
                'travel_origin' => $this->work_type === 'out_of_office' ? $this->travel_origin : null,
                'travel_destination' => $this->work_type === 'out_of_office' ? $this->travel_destination : null,
                'travel_purpose' => $this->work_type === 'out_of_office' ? $this->travel_purpose : null,
                'overtime_hours' => $this->overtime_hours,
                'meal_allowance_amount' => $this->meal_allowance_amount,
                'total_amount' => $this->total_amount,
                'has_overtime' => $this->overtime_hours > 0,
                'has_meal_allowance' => $this->meal_allowance_amount > 0,
                'remarks' => $this->remarks,
                'status' => 'pending_approval', // Direct submission
                'submitted_at' => now(), // Set submission timestamp
            ];

            if ($this->isEditing) {
                $this->claim->update($claimData);
            } else {
                $claimData['claim_number'] = Claim::generateClaimNumber();
                $this->claim = Claim::create($claimData);
            }

            // Handle file uploads
            $this->handleFileUploads();

            // TODO: Send notification to manager

            session()->flash('success', 'Claim submitted for approval successfully!');
            return redirect()->route('staff.dashboard');

        } catch (\Exception $e) {
            session()->flash('error', 'Error submitting claim. Please try again.');
        }
    }

    public function removeDocument($documentId)
    {
        try {
            $document = ClaimDocument::findOrFail($documentId);

            // Check if user owns this claim
            if ($document->claim->user_id !== Auth::id()) {
                session()->flash('error', 'Unauthorized action.');
                return;
            }

            // Delete the file from storage
            if (Storage::exists($document->file_path)) {
                Storage::delete($document->file_path);
            }

            // Delete the database record
            $document->delete();

            session()->flash('success', 'Document removed successfully.');

            // Refresh the claim to update the relationship
            $this->claim = $this->claim->fresh();

        } catch (\Exception $e) {
            session()->flash('error', 'Error removing document. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.claims.claim-form')->layout('layouts.app-claim');
    }
}
