<?php

use App\Http\Controllers\StaffController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', LoginController::class);
Route::middleware('auth:sanctum')->post('/logout', LogoutController::class);

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return response()->json([
        'user' => $request->user()
    ]);
});

Route::middleware('auth:sanctum')->post('/reservations/store', [ReservationController::class, 'store']);
Route::middleware('auth:sanctum')->get('/reservations', [ReservationController::class, 'index']);
Route::middleware('auth:sanctum')->get('/availfacility1/{id}/{date}', [FacilityController::class, 'availability1']);
Route::middleware('auth:sanctum')->get('/availfacility2/{id}/{date}', [FacilityController::class, 'availability2']);
Route::middleware('auth:sanctum')->get('/availfacility3/{id}/{date}', [FacilityController::class, 'availability3']);
Route::middleware('auth:sanctum')->get('/amenities', [AmenityController::class, 'index']);

Route::get('/refreshpubkey', function () {
    try {
        $publicKey = file_get_contents(storage_path('app/keys/public.pem'));

        return response()->json([
            'public_key' => $publicKey,
            'updated_at' => now()->toISOString()
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to get public key'], 500);
    }
});

Route::prefix('staff')->group(function () {
    Route::post('/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [StaffAuthController::class, 'logout']);
        Route::get('/me', [StaffAuthController::class, 'me']);
        Route::post('/verify-reservation', [StaffController::class, 'verifyReservation']);
        Route::post('/check-in', [StaffController::class, 'checkIn']);

        // Your future QR code routes will go here
        //Route::post('/verify-qr', [StaffController::class, 'verifyQr']);
        //Route::post('/check-in', [StaffController::class, 'checkIn']);
    });
});
