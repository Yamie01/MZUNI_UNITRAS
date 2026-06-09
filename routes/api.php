<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and will be assigned
| to the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\TrackingController;

Route::middleware('auth:sanctum')->group(function () {
    // Vehicle tracking (owner/driver)
    Route::post('/vehicles/{vehicle}/location', [TrackingController::class, 'updateVehicleLocation']);
    Route::get('/vehicles/{vehicle}/location', [TrackingController::class, 'getVehicleLocation']);
    Route::get('/vehicles/{vehicle}/history', [TrackingController::class, 'getVehicleHistory']);
    
    // Bike tracking (admin/renter)
    Route::post('/bikes/{bike}/location', [TrackingController::class, 'updateBikeLocation']);
    Route::get('/bikes/{bike}/location', [TrackingController::class, 'getBikeLocation']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});