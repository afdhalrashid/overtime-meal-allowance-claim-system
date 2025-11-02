<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimSubmissionDeadlineReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $daysUntilDeadline
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Claim Submission Deadline Reminder')
            ->greeting('Hello ' . $notifiable->name)
            ->line('⏰ This is a friendly reminder about the claim submission deadline.')
            ->line('You have ' . $this->daysUntilDeadline . ' day(s) remaining to submit your overtime/meal allowance claims for this month.')
            ->line('Please ensure you submit your claims before the deadline to avoid any delays in processing.')
            ->action('Submit Claim', url('/dashboard'))
            ->line('If you have any questions, please contact your supervisor or HR department.');
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'deadline_reminder',
            'days_until_deadline' => $this->daysUntilDeadline,
            'message' => 'Claim submission deadline reminder: ' . $this->daysUntilDeadline . ' days remaining',
        ];
    }
}
