<?php
// app/Notifications/CustomResetPassword.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return (new MailMessage)
            ->subject('Password Reset Request - HOA Montaña')
            ->markdown('emails.auth.password-reset', [
                'token' => $this->token,
                'user' => $notifiable
            ])
            ->line('Please use the reset code above in the HOA Montaña mobile app to reset your password.')
            ->line('This reset code will expire in 60 minutes.')
            ->line('If you did not request a password reset, please ignore this email.');
    }
}
