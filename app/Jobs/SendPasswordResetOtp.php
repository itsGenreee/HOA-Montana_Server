<?php
// app/Jobs/SendPasswordResetOtp.php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPasswordResetOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $otp;

    public function __construct(User $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function handle(): void
    {
        try {
            Log::info('Sending password reset OTP via job', [
                'email' => $this->user->email,
                'job_id' => $this->job->getJobId()
            ]);

            // This will now use your CustomResetPassword notification
            $this->user->notify(new \App\Notifications\CustomResetPassword($this->otp));

            Log::info('Password reset OTP sent successfully via job', [
                'email' => $this->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset OTP via job', [
                'email' => $this->user->email,
                'error' => $e->getMessage()
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error('SendPasswordResetOtp job failed', [
            'email' => $this->user->email,
            'error' => $exception->getMessage()
        ]);
    }
}
