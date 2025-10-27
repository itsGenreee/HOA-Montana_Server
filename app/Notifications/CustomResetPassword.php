<?php
// app/Notifications/CustomResetPassword.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomResetPassword extends Notification
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
        Log::info('🔧 Building CustomResetPassword email', [
            'email' => $notifiable->email,
            'token' => $this->token
        ]);

        try {
            $mail = (new MailMessage)
                ->subject('Password Reset Request - HOA Montaña')
                ->markdown('emails.auth.password-reset', [
                    'token' => $this->token,
                    'user' => $notifiable
                ])
                ->line('Please use the reset code above in the HOA Montaña mobile app to reset your password.')
                ->line('This reset code will expire in 60 minutes.')
                ->line('If you did not request a password reset, please ignore this email.');

            Log::info('✅ Email message built successfully', [
                'email' => $notifiable->email
            ]);

            return $mail;

        } catch (\Exception $e) {
            Log::error('❌ Error building email message', [
                'email' => $notifiable->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function failed(\Exception $e)
    {
        Log::error('💥 CustomResetPassword notification FAILED', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
