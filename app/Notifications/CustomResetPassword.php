<?php
// app/Notifications/CustomResetPassword.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset Request - HOA Montaña')
            ->view('emails.auth.password-reset', [
                'token' => $this->token,
                'user' => $notifiable
            ]);
    }

private function buildHtmlEmail($token)
{
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            .otp-code {
                background: #ffdbcd;
                color: #360f00;
                padding: 20px;
                border-radius: 4px;
                text-align: center;
                font-size: 24px;
                font-weight: bold;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <h1 style='color: #201a18; font-size: 24px; font-weight: bold;'>Password Reset Request</h1>
        <p>You are receiving this email because we received a password reset request for your account.</p>

        <h2>Your Reset Code</h2>
        <div class='otp-code'>{$token}</div>

        <p>This reset code will expire in 60 minutes.</p>
        <p>Please use this code in the HOA Montaña mobile app to reset your password.</p>
        <p>If you did not request a password reset, no further action is required.</p>

        <p>Thanks,<br>HOA Montaña Team</p>
    </body>
    </html>
    ";
}

    public function failed(\Exception $e)
    {
        Log::error('💥 CustomResetPassword notification FAILED', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
