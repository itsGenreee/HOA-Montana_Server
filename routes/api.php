<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AmenityController;

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
