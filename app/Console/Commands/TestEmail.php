<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email? : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify Mailtrap configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';

        $this->info("Sending test email to: {$email}");

        try {
            Mail::raw('This is a test email from your Laravel Claim System to verify Mailtrap integration.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Mailtrap Integration')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('✅ Test email sent successfully!');
            $this->info('Check your Mailtrap inbox at: https://mailtrap.io/inboxes');

        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
