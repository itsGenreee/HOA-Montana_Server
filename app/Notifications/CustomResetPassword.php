<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail']; // Use standard 'mail' channel - it will now use Brevo API
    }

    public function toMail($notifiable)
    {
        try {
            Log::info('Building password reset email', [
                'email' => $notifiable->email,
                'token' => $this->token
            ]);

            return (new MailMessage)
                ->subject('Password Reset Request - HOA Montaña')
                ->view('emails.auth.password-reset', [
                    'token' => $this->token,
                    'user' => $notifiable,
                ]);

        } catch (\Exception $e) {
            Log::error('Failed to build password reset email', [
                'email' => $notifiable->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function failed(\Exception $e)
    {
        Log::error('💥 CustomResetPassword notification FAILED', [
            'error' => $e->getMessage(),
        ]);
    }
}
