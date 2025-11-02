<?php

namespace App\Notifications;

use App\Models\Claim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClaimStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Claim $claim,
        public string $previousStatus,
        public ?string $rejectionReason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Claim Status Updated - ' . $this->claim->claim_number)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your overtime/meal allowance claim has been updated.');

        switch ($this->claim->status) {
            case 'approved':
                $message->line('🎉 Great news! Your claim has been approved.')
                       ->line('Your claim for ' . $this->claim->duty_date->format('M d, Y') . ' has been approved by your supervisor.')
                       ->line('Total Amount: ' . \App\Models\SystemSetting::formatCurrency($this->claim->total_amount))
                       ->action('View Claim', url('/dashboard'));
                break;

            case 'rejected':
                $message->line('❌ Unfortunately, your claim has been rejected.')
                       ->line('Claim Date: ' . $this->claim->duty_date->format('M d, Y'));

                if ($this->rejectionReason) {
                    $message->line('Reason: ' . $this->rejectionReason);
                }

                $message->line('You may submit a new claim with the correct information.')
                       ->action('Submit New Claim', url('/dashboard'));
                break;

            case 'processed':
                $message->line('✅ Your claim has been processed by HR.')
                       ->line('Your claim is now ready for payroll processing.')
                       ->line('You should receive payment in the next payroll cycle.')
                       ->action('View Claim', url('/dashboard'));
                break;

            case 'paid':
                $message->line('💰 Your claim has been paid!')
                       ->line('Payment for your overtime/meal allowance claim has been processed.')
                       ->line('Amount: ' . \App\Models\SystemSetting::formatCurrency($this->claim->total_amount))
                       ->line('You should see this amount in your next payslip.')
                       ->action('View Claim', url('/dashboard'));
                break;
        }

        return $message->line('Thank you for using our claim system!');
    }

    public function toArray($notifiable): array
    {
        $data = [
            'claim_id' => $this->claim->id,
            'claim_number' => $this->claim->claim_number,
            'status' => $this->claim->status,
            'previous_status' => $this->previousStatus,
            'duty_date' => $this->claim->duty_date->format('M d, Y'),
            'total_amount' => $this->claim->total_amount,
        ];

        if ($this->rejectionReason) {
            $data['rejection_reason'] = $this->rejectionReason;
        }

        return $data;
    }
}
