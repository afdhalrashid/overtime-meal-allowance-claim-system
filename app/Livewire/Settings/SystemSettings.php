<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\SystemSetting;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SystemSettings extends Component
{
    public $currency_symbol;
    public $currency_code;
    public $currency_position;
    public $overtime_rate_weekday;
    public $overtime_rate_weekend;
    public $overtime_rate_holiday;
    public $meal_allowance_amount;
    public $exclude_travel_time_for_non_drivers;

    protected $rules = [
        'currency_symbol' => 'required|string|max:10',
        'currency_code' => 'required|string|max:3',
        'currency_position' => 'required|in:before,after',
        'overtime_rate_weekday' => 'required|numeric|min:0',
        'overtime_rate_weekend' => 'required|numeric|min:0',
        'overtime_rate_holiday' => 'required|numeric|min:0',
        'meal_allowance_amount' => 'required|numeric|min:0',
        'exclude_travel_time_for_non_drivers' => 'boolean',
    ];

    public function mount()
    {
        // Load current settings
        $this->currency_symbol = SystemSetting::get('currency_symbol', 'RM');
        $this->currency_code = SystemSetting::get('currency_code', 'MYR');
        $this->currency_position = SystemSetting::get('currency_position', 'before');
        $this->overtime_rate_weekday = SystemSetting::get('overtime_rate_weekday', 25.00);
        $this->overtime_rate_weekend = SystemSetting::get('overtime_rate_weekend', 35.00);
        $this->overtime_rate_holiday = SystemSetting::get('overtime_rate_holiday', 50.00);
        $this->meal_allowance_amount = SystemSetting::get('meal_allowance_amount', 15.00);
        $this->exclude_travel_time_for_non_drivers = SystemSetting::get('exclude_travel_time_for_non_drivers', true);
    }

    public function save()
    {
        $this->validate();

        // Save currency settings
        SystemSetting::set('currency_symbol', $this->currency_symbol, 'string', 'Currency symbol');
        SystemSetting::set('currency_code', $this->currency_code, 'string', 'Currency code');
        SystemSetting::set('currency_position', $this->currency_position, 'string', 'Currency position: before or after amount');

        // Save rate settings
        SystemSetting::set('overtime_rate_weekday', $this->overtime_rate_weekday, 'number', 'Overtime rate per hour for weekdays');
        SystemSetting::set('overtime_rate_weekend', $this->overtime_rate_weekend, 'number', 'Overtime rate per hour for weekends');
        SystemSetting::set('overtime_rate_holiday', $this->overtime_rate_holiday, 'number', 'Overtime rate per hour for public holidays');
        SystemSetting::set('meal_allowance_amount', $this->meal_allowance_amount, 'number', 'Meal allowance amount');
        SystemSetting::set('exclude_travel_time_for_non_drivers', $this->exclude_travel_time_for_non_drivers, 'boolean', 'Exclude travel time from overtime calculation for employees without driving role');

        session()->flash('success', 'System settings updated successfully!');
    }

    public function render()
    {
        return view('livewire.settings.system-settings', [
            'title' => 'System Settings'
        ]);
    }
}
