<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClaimSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Claim $claim
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Claim Requires Your Approval - ' . $this->claim->claim_number)
            ->greeting('Hello ' . $notifiable->name)
            ->line('📋 A new overtime/meal allowance claim has been submitted and requires your approval.')
            ->line('**Claim Details:**')
            ->line('Employee: ' . $this->claim->user->name)
            ->line('Department: ' . ($this->claim->user->department->name ?? 'N/A'))
            ->line('Duty Date: ' . $this->claim->duty_date->format('M d, Y'))
            ->line('Overtime Hours: ' . number_format($this->claim->overtime_hours, 1) . ' hours')
            ->line('Total Amount: ' . \App\Models\SystemSetting::formatCurrency($this->claim->total_amount))
            ->line('Description: ' . $this->claim->description)
            ->action('Review Claim', url('/dashboard'))
            ->line('Please review and approve or reject this claim at your earliest convenience.');
    }

    public function toArray($notifiable): array
    {
        return [
            'claim_id' => $this->claim->id,
            'claim_number' => $this->claim->claim_number,
            'employee_name' => $this->claim->user->name,
            'department' => $this->claim->user->department->name ?? 'N/A',
            'duty_date' => $this->claim->duty_date->format('M d, Y'),
            'overtime_hours' => $this->claim->overtime_hours,
            'total_amount' => $this->claim->total_amount,
            'type' => 'new_claim_submitted',
        ];
    }
}
