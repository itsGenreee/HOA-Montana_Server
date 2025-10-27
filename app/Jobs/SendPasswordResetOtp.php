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
use Illuminate\Support\Facades\Mail;

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
            Log::info('=== SEND PASSWORD RESET OTP JOB STARTED ===', [
                'email' => $this->user->email,
                'otp' => $this->otp,
                'job_id' => $this->job->getJobId(),
                'queue' => $this->queue,
                'connection' => config('queue.default')
            ]);

            // Test direct email first
            Log::info('Testing mail configuration...');

            // This will now use your CustomResetPassword notification
            $this->user->notify(new \App\Notifications\CustomResetPassword($this->otp));

            Log::info('=== PASSWORD RESET OTP SENT SUCCESSFULLY ===', [
                'email' => $this->user->email
            ]);

        } catch (\Exception $e) {
            Log::error('!!! FAILED TO SEND PASSWORD RESET OTP !!!', [
                'email' => $this->user->email,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString()
            ]);

            $this->fail($e);
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error('!!! SEND PASSWORD RESET OTP JOB FAILED !!!', [
            'email' => $this->user->email,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
