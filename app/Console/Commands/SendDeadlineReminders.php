<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\ClaimSubmissionDeadlineReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'claims:deadline-reminders {days=7 : Days before deadline to send reminder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send deadline reminder notifications to staff members';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->argument('days');

        $this->info("Sending deadline reminders for {$days} days before deadline...");

        // Get all staff members
        $staff = User::where('role', 'staff')->get();

        $sentCount = 0;

        foreach ($staff as $user) {
            // Send reminder notification
            $user->notify(new ClaimSubmissionDeadlineReminder($days));
            $sentCount++;
        }

        $this->info("Successfully sent {$sentCount} deadline reminder notifications.");

        return Command::SUCCESS;
    }
}
