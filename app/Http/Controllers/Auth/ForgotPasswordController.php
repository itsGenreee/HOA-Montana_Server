<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            // Return success even if user doesn't exist for security
            return response()->json([
                'status' => 'success',
                'message' => 'If an account with that email exists, we have sent a password reset code.'
            ]);
        }

        // Generate 6-digit numeric OTP (as string)
        $otp = (string) random_int(100000, 999999);

        // Store OTP in database (hash it for security)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now()
            ]
        );

        // Send email with OTP
        try {
            $user->sendPasswordResetNotification($otp);

            return response()->json([
                'status' => 'success',
                'message' => 'If an account with that email exists, we have sent a password reset code.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send reset email. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP before allowing password reset
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify OTP
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'status' => 'error',
                'message' => 'No reset request found for this email.'
            ], 400);
        }

        // Check if OTP matches
        if (!Hash::check($request->otp, $resetRecord->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code.'
            ], 400);
        }

        // Check if OTP is expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'OTP has expired. Please request a new one.'
            ], 400);
        }

        // OTP is valid - you could return a verification token here if needed
        return response()->json([
            'status' => 'success',
            'message' => 'OTP verified successfully.',
            'verified' => true
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|digits:6',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify OTP manually
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired reset code.'
            ], 400);
        }

        // Check if OTP is expired (60 minutes)
        if (Carbon::parse($resetRecord->created_at)->diffInMinutes(Carbon::now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'status' => 'error',
                'message' => 'Reset code has expired.'
            ], 400);
        }

        // Reset password
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->setRememberToken(Str::random(60));
            $user->save();

            // Delete used OTP
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            event(new PasswordReset($user));

            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not found.'
        ], 400);
    }
}
