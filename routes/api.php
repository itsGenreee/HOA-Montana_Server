<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\PaymentController;
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


Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('payment')->group(function () {
        Route::post('/create-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
        Route::post('/webhook', [PaymentController::class, 'handleWebhook']);
        Route::get('/status', [PaymentController::class, 'checkPaymentStatus']);
    });

    Route::post('/reservations/store', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/availfacility1/{id}/{date}', [FacilityController::class, 'availability1']);
    Route::get('/availfacility2/{id}/{date}', [FacilityController::class, 'availability2']);
    Route::get('/availfacility3/{id}/{date}', [FacilityController::class, 'availability3']);
    Route::get('/amenities', [AmenityController::class, 'index']);

    Route::post('/logout', LogoutController::class);

    Route::get('/me', function (Request $request) {
        return response()->json([
            'user' => $request->user()
        ]);
    });

});


Route::prefix('staff')->group(function () {
    Route::post('/login', [StaffAuthController::class, 'login']);

    Route::middleware('auth:staff')->group(function () {
        Route::post('/logout', [StaffAuthController::class, 'logout']);
        Route::get('/me', [StaffAuthController::class, 'me']);
        Route::post('/verify-reservation', [StaffController::class, 'verifyReservation']);
        Route::post('/check-in', [StaffController::class, 'checkIn']);

        // Counts of Reservations for Dashboard
        Route::get('/reservations/staff-checked-in-count', [ReservationController::class, 'staffCheckedInCount']);
        Route::get('/reservations/pending-reservation', [ReservationController::class, 'pendingReservationCount']);
        Route::get('/reservations/today-reservation', [ReservationController::class, 'todayReservationCount']);
        Route::get('/reservations/total-reservation', [ReservationController::class, 'totalReservationCount']);
        Route::get('/dashboard/stats', [ReservationController::class, 'getDashboardStats']);


        Route::get('/availfacility1/{id}/{date}', [FacilityController::class, 'availability1']);
        Route::get('/availfacility2/{id}/{date}', [FacilityController::class, 'availability2']);
        Route::get('/availfacility3/{id}/{date}', [FacilityController::class, 'availability3']);

        Route::get('/amenities', [AmenityController::class, 'index']);
        Route::get('/reservations/pending', [StaffController::class, 'getPendingReservations']);
        Route::get('/reservations/debug-conflicting-reservations/{id}', [StaffController::class, 'debugConflictingReservations']);
        Route::post('/reservations/store-by-staff', [ReservationController::class, 'storeByStaff']);
        Route::post('/reservations/{id}/confirm-pending-reservation', [StaffController::class, 'confirmReservation']);
        Route::post('/reservations/{id}/cancel-pending-reservation', [StaffController::class, 'cancelReservation']);
        Route::post('/reservations/{id}/cancel-confirmed-reservation', [StaffController::class, 'cancelConfirmedReservation']);

        Route::get('/users/unverified', [UserController::class, 'getUnverifiedUsers']);
        Route::get('/users/verified', [UserController::class, 'getVerifiedUsers']);
        Route::get('/users', [UserController::class, 'getAllUsers']);
        Route::post('/users/{id}/verify', [UserController::class, 'verifyUser']);
        Route::post('/users/{id}/reject', [UserController::class, 'rejectUser']);

        Route::get('/staff-accounts', [StaffAuthController::class, 'index']);
        Route::post('/staff-accounts/register', [StaffAuthController::class, 'register']);
        Route::post('/staff-accounts/{id}/update', [StaffAuthController::class, 'update']);
        Route::post('/staff-accounts/{id}/delete', [StaffAuthController::class, 'destroy']);
    });
});
