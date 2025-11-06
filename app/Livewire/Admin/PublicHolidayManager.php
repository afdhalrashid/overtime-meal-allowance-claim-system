<?php

namespace App\Livewire\Admin;

use App\Models\PublicHoliday;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.app')]
class PublicHolidayManager extends Component
{
    public $yearFilter;
    public $showModal = false;

    // Form fields
    public $name = '';
    public $date = '';
    public $description = '';
    public $is_active = true;
    public $editingId = null;

    public function mount()
    {
        $this->yearFilter = now()->year;
    }

    public function updatedYearFilter()
    {
        // This will trigger a re-render when year filter changes
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($holidayId)
    {
        $holiday = PublicHoliday::findOrFail($holidayId);

        $this->editingId = $holiday->id;
        $this->name = $holiday->name;
        $this->date = $holiday->date->format('Y-m-d');
        $this->description = $holiday->description ?? '';
        $this->is_active = $holiday->is_active;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->date = '';
        $this->description = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        // Check if a holiday with the same name and date already exists (except for current editing record)
        $existingHoliday = PublicHoliday::where('date', $this->date)
            ->where('name', $this->name)
            ->when($this->editingId, function ($query) {
                return $query->where('id', '!=', $this->editingId);
            })
            ->first();

        if ($existingHoliday) {
            $this->addError('date', 'A public holiday with this name already exists on this date.');
            return;
        }

        $data = [
            'name' => $this->name,
            'date' => $this->date,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            PublicHoliday::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Public holiday updated successfully.');
        } else {
            PublicHoliday::create($data);
            session()->flash('success', 'Public holiday created successfully.');
        }

        $this->closeModal();
    }

    public function deleteHoliday($holidayId)
    {
        PublicHoliday::findOrFail($holidayId)->delete();
        session()->flash('success', 'Public holiday deleted successfully.');
    }

    public function toggleStatus($holidayId)
    {
        $holiday = PublicHoliday::findOrFail($holidayId);
        $holiday->update(['is_active' => !$holiday->is_active]);

        $status = $holiday->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Public holiday {$status} successfully.");
    }

    public function bulkImportMalaysianHolidays()
    {
        $year = $this->yearFilter;

        // Standard Malaysian public holidays for the year
        $malaysianHolidays = [
            [
                'name' => 'New Year\'s Day',
                'date' => "{$year}-01-01",
                'description' => 'New Year\'s Day celebration',
            ],
            [
                'name' => 'Federal Territory Day',
                'date' => "{$year}-02-01",
                'description' => 'Federal Territory Day (KL, Putrajaya, Labuan)',
            ],
            [
                'name' => 'Labour Day',
                'date' => "{$year}-05-01",
                'description' => 'International Workers\' Day',
            ],
            [
                'name' => 'Wesak Day',
                'date' => "{$year}-05-12", // This varies each year, placeholder date
                'description' => 'Buddha\'s Birthday celebration',
            ],
            [
                'name' => 'King\'s Birthday',
                'date' => "{$year}-06-07", // First Monday of June, placeholder
                'description' => 'Yang di-Pertuan Agong\'s Birthday',
            ],
            [
                'name' => 'Merdeka Day',
                'date' => "{$year}-08-31",
                'description' => 'Independence Day celebration',
            ],
            [
                'name' => 'Malaysia Day',
                'date' => "{$year}-09-16",
                'description' => 'Formation of Malaysia',
            ],
            [
                'name' => 'Deepavali',
                'date' => "{$year}-10-31", // This varies each year, placeholder
                'description' => 'Festival of Lights',
            ],
            [
                'name' => 'Christmas Day',
                'date' => "{$year}-12-25",
                'description' => 'Christmas celebration',
            ],
        ];

        $imported = 0;
        $skipped = 0;

        foreach ($malaysianHolidays as $holidayData) {
            try {
                // Check for existing holiday with same name and date to prevent duplicates
                $existing = PublicHoliday::where('date', $holidayData['date'])
                    ->where('name', $holidayData['name'])
                    ->first();

                if (!$existing) {
                    PublicHoliday::create([
                        'name' => $holidayData['name'],
                        'date' => $holidayData['date'],
                        'description' => $holidayData['description'],
                        'is_active' => true,
                    ]);
                    $imported++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                // Skip if constraint violation occurs
                $skipped++;
            }
        }

        if ($imported > 0 && $skipped > 0) {
            session()->flash('success', "Imported {$imported} new Malaysian public holidays for {$year}. Skipped {$skipped} existing holidays.");
        } elseif ($imported > 0) {
            session()->flash('success', "Imported {$imported} Malaysian public holidays for {$year}.");
        } else {
            session()->flash('success', "All Malaysian public holidays for {$year} already exist. No new holidays imported.");
        }
    }

    public function render()
    {
        $holidays = PublicHoliday::whereYear('date', $this->yearFilter)
            ->orderBy('date')
            ->get();

        $years = PublicHoliday::selectRaw('DISTINCT strftime("%Y", date) as year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->filter()
            ->values();

        // Add current year if not in the list
        if (!$years->contains($this->yearFilter)) {
            $years->prepend($this->yearFilter);
            $years = $years->sort()->reverse()->values();
        }

        return view('livewire.admin.public-holiday-manager', [
            'holidays' => $holidays,
            'years' => $years,
        ]);
    }
}
