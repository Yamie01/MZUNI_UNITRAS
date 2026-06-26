<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\RideController;
use App\Http\Controllers\API\BikeController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\RentalController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\TrackingController;
use Illuminate\Support\Facades\Route;

// ================================
// PUBLIC API ROUTES
// ================================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// ================================
// PROTECTED API ROUTES (Requires Token)
// ================================
Route::middleware('auth:sanctum')->group(function () {

    // ---------- AUTH ----------
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // ---------- RIDES ----------
    Route::get('/rides', [RideController::class, 'index']);
    Route::get('/rides/{id}', [RideController::class, 'show']);
    Route::post('/rides/{id}/book', [BookingController::class, 'store']);
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // ---------- BIKES ----------
    Route::get('/bikes', [BikeController::class, 'index']);
    Route::get('/bikes/{id}', [BikeController::class, 'show']);
    Route::post('/bikes/{id}/rent', [RentalController::class, 'store']);
    Route::get('/rentals', [RentalController::class, 'index']);
    Route::get('/rentals/{id}', [RentalController::class, 'show']);
    Route::post('/rentals/{id}/cancel', [RentalController::class, 'cancel']);
    Route::post('/rentals/{id}/return', [RentalController::class, 'returnBike']);

    // ---------- PAYMENT ----------
    Route::post('/payment/initiate', [PaymentController::class, 'initiateMobile']);
    Route::get('/payment/status/{reference}', [PaymentController::class, 'status']);

    // ---------- TRACKING ----------
    Route::post('/tracking/vehicle/{vehicle}', [TrackingController::class, 'updateVehicleLocation']);
    Route::post('/tracking/bike/{bike}', [TrackingController::class, 'updateBikeLocation']);
    Route::get('/tracking/ride/{booking}', [TrackingController::class, 'getRideTracking']);
    Route::get('/tracking/bike/{rental}', [TrackingController::class, 'getBikeTracking']);

    // ---------- SUBSCRIPTION ----------
    Route::get('/subscription', [SubscriptionController::class, 'status']);
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('/bike/activate', [App\Http\Controllers\API\BikeController::class, 'activate'])->middleware('auth');
});