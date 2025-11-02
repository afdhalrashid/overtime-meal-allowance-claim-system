<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Notifications\ClaimStatusUpdated;
use App\Notifications\NewClaimSubmitted;
use Illuminate\Support\Facades\Notification;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_number',
        'user_id',
        'duty_date',
        'start_time',
        'end_time',
        'work_type',
        'has_overtime',
        'has_meal_allowance',
        'overtime_hours',
        'meal_allowance_amount',
        'total_amount',
        'travel_start_time',
        'travel_end_time',
        'travel_origin',
        'travel_destination',
        'travel_purpose',
        'travel_hours',
        'status',
        'submitted_at',
        'remarks',
        'approval_remarks',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'processed_by',
        'processed_at',
        'process_remarks',
        'paid_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'duty_date' => 'date',
            'start_time' => 'datetime:H:i',
            'end_time' => 'datetime:H:i',
            'travel_start_time' => 'datetime:H:i',
            'travel_end_time' => 'datetime:H:i',
            'has_overtime' => 'boolean',
            'has_meal_allowance' => 'boolean',
            'overtime_hours' => 'decimal:2',
            'meal_allowance_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'travel_hours' => 'decimal:2',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'processed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::updated(function (Claim $claim) {
            // Notify user when claim status changes from pending_approval
            if ($claim->isDirty('status') && $claim->getOriginal('status') === 'pending_approval') {
                $previousStatus = $claim->getOriginal('status');

                $claim->user->notify(new ClaimStatusUpdated(
                    $claim,
                    $previousStatus,
                    $claim->rejection_reason
                ));
            }

            // Notify approvers when claim is submitted (status changes to pending_approval)
            if ($claim->isDirty('status') && $claim->status === 'pending_approval') {
                $approvers = User::where('role', 'approver')
                    ->where('department_id', $claim->user->department_id)
                    ->get();

                if ($approvers->isNotEmpty()) {
                    Notification::send($approvers, new NewClaimSubmitted($claim));
                }
            }
        });
    }

    /**
     * Get the user who submitted this claim.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved this claim.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who processed this claim.
     */
    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the user who marked this claim as paid.
     */
    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Get the documents attached to this claim.
     */
    public function documents()
    {
        return $this->hasMany(ClaimDocument::class);
    }

    /**
     * Get the attendance record document.
     */
    public function attendanceRecord()
    {
        return $this->documents()->where('document_type', 'attendance_record');
    }

    /**
     * Get the supporting documents.
     */
    public function supportingDocuments()
    {
        return $this->documents()->where('document_type', 'supporting_document');
    }

    /**
     * Get the first attendance record document.
     */
    public function getAttendanceRecordDocumentAttribute()
    {
        return $this->attendanceRecord()->first();
    }

    /**
     * Get all supporting documents as collection.
     */
    public function getSupportingDocumentsCollectionAttribute()
    {
        return $this->supportingDocuments()->get();
    }

    /**
     * Check if claim is editable by the user.
     */
    public function isEditable(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if claim can be submitted.
     */
    public function canBeSubmitted(): bool
    {
        return $this->status === 'draft' && $this->hasRequiredDocuments();
    }

    /**
     * Check if claim has required documents.
     */
    public function hasRequiredDocuments(): bool
    {
        return $this->attendanceRecord()->exists() &&
               $this->supportingDocuments()->exists();
    }

    /**
     * Check if claim submission deadline has passed.
     */
    public function isSubmissionDeadlinePassed(): bool
    {
        $deadline = $this->duty_date->addMonths(2);
        return now()->isAfter($deadline);
    }

    /**
     * Get days remaining until submission deadline.
     */
    public function daysUntilDeadline(): int
    {
        $deadline = $this->duty_date->addMonths(2);
        return max(0, now()->diffInDays($deadline, false));
    }

    /**
     * Generate unique claim number.
     */
    public static function generateClaimNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastClaim = static::whereYear('created_at', $year)
                          ->whereMonth('created_at', now()->month)
                          ->orderBy('id', 'desc')
                          ->first();

        $sequence = $lastClaim ? (int) substr($lastClaim->claim_number, -4) + 1 : 1;

        return sprintf('CLM%s%s%04d', $year, $month, $sequence);
    }
}
