<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the deadline reminders command
Schedule::command('claims:deadline-reminders', [7])
    ->weeklyOn(1, '09:00') // Every Monday at 9:00 AM
    ->description('Send weekly deadline reminders to staff (7 days before deadline)');

// Additional deadline reminder - 3 days before deadline
Schedule::command('claims:deadline-reminders', [3])
    ->weeklyOn(4, '09:00') // Every Thursday at 9:00 AM
    ->description('Send deadline reminders to staff (3 days before deadline)');

// Monthly reminder - 1 day before deadline
Schedule::command('claims:deadline-reminders', [1])
    ->monthlyOn(29, '09:00') // 29th of each month at 9:00 AM (assuming deadline is 30th)
    ->description('Send urgent deadline reminders to staff (1 day before deadline)');

// Process queued jobs every minute
Schedule::command('queue:work --stop-when-empty')
    ->everyMinute()
    ->description('Process all pending email notifications and jobs');
