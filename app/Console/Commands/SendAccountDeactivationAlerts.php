<?php
namespace App\Console\Commands;

use App\Mail\EmailNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAccountDeactivationAlerts extends Command
{
    protected $signature   = 'send:account-deactivation-alerts';
    protected $description = 'Send account deactivation alerts to users 2 days before deactivation';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Calculate the deactivation date (2 days from now)
        $twoDaysLater = Carbon::now()->addDays(2)->format('Y-m-d H:i:s');

        // Query users whose activation_expires date is exactly 2 days from now
        $users = User::where('activation_expires', $twoDaysLater)->where('is_active', 1)->get();

        foreach ($users as $user) {
            // Prepare email details
            $emailData = [
                'subject'    => 'Your Account Will Be Deactivated Soon',
                'email_body' => view('emails.account_deactivation_alert', compact('user'))->render(),
                'to'         => $user->email,
            ];

            // Send the email
            Mail::send(new EmailNotification($emailData));
        }

        $this->info('Account deactivation alerts sent successfully.');
    }
}
