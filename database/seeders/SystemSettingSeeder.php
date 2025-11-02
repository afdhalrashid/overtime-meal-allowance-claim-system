<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Working hours
            ['key' => 'working_hours_start', 'value' => '08:00', 'type' => 'string', 'description' => 'Standard working hours start time'],
            ['key' => 'working_hours_end', 'value' => '17:00', 'type' => 'string', 'description' => 'Standard working hours end time'],

            // Overtime rates
            ['key' => 'overtime_rate_weekday', 'value' => '25.00', 'type' => 'number', 'description' => 'Overtime rate per hour for weekdays'],
            ['key' => 'overtime_rate_weekend', 'value' => '35.00', 'type' => 'number', 'description' => 'Overtime rate per hour for weekends'],
            ['key' => 'overtime_rate_holiday', 'value' => '50.00', 'type' => 'number', 'description' => 'Overtime rate per hour for public holidays'],

            // Meal allowance
            ['key' => 'meal_allowance_amount', 'value' => '15.00', 'type' => 'number', 'description' => 'Meal allowance amount'],
            ['key' => 'meal_allowance_minimum_hours', 'value' => '2', 'type' => 'number', 'description' => 'Minimum overtime hours to qualify for meal allowance'],
            ['key' => 'meal_allowance_time_threshold', 'value' => '19:00', 'type' => 'string', 'description' => 'Time threshold for meal allowance eligibility'],

            // Submission settings
            ['key' => 'submission_deadline_months', 'value' => '2', 'type' => 'number', 'description' => 'Months from duty date to submit claim'],
            ['key' => 'submission_deadline_day', 'value' => '9', 'type' => 'number', 'description' => 'Day of month for processing deadline'],
            ['key' => 'deadline_warning_days', 'value' => '7', 'type' => 'number', 'description' => 'Days before deadline to send warning'],
            ['key' => 'monthly_reminder_day', 'value' => '5', 'type' => 'number', 'description' => 'Day of month to send reminder'],

            // File upload settings
            ['key' => 'max_file_size_mb', 'value' => '5', 'type' => 'number', 'description' => 'Maximum file size in MB'],
            ['key' => 'allowed_file_types', 'value' => '["pdf","jpg","jpeg","png","docx"]', 'type' => 'json', 'description' => 'Allowed file types for upload'],

            // Email settings
            ['key' => 'email_from_address', 'value' => 'noreply@company.edu', 'type' => 'string', 'description' => 'From email address'],
            ['key' => 'email_from_name', 'value' => 'Claim System', 'type' => 'string', 'description' => 'From email name'],

            // Currency settings
            ['key' => 'currency_symbol', 'value' => 'RM', 'type' => 'string', 'description' => 'Currency symbol'],
            ['key' => 'currency_code', 'value' => 'MYR', 'type' => 'string', 'description' => 'Currency code'],
            ['key' => 'currency_position', 'value' => 'before', 'type' => 'string', 'description' => 'Currency position: before or after amount'],
        ];

        foreach ($settings as $setting) {
            \App\Models\SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
