<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
public function __invoke(Request $request): JsonResponse
{
    $request->validate([
        'email' => ['required', 'string', 'email'],
        'password' => ['required', 'string'],
    ]);

    // Rate limiting with custom duration
    $throttleKey = strtolower($request->email) . '|' . $request->ip();
    $maxAttempts = 5;
    $decaySeconds = 60; // 1 minute lockout

    if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        throw ValidationException::withMessages([
            'email' => [__('auth.throttle', ['seconds' => $seconds])],
        ]);
    }

    // This hits the rate limiter with custom decay time
    RateLimiter::hit($throttleKey, $decaySeconds);

    // Single authentication attempt with generic error
    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The email or password is incorrect.'],
        ]);
    }

    // Clear rate limiter on successful login
    RateLimiter::clear($throttleKey);

    $user = User::where('email', $request->email)->firstOrFail();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token,
    ]);
}
}
