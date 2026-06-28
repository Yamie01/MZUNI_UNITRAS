<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\OfferRideController;
use App\Http\Controllers\BecomeVehicleOwnerController;

// Middleware
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VehicleOwnerMiddleware;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VehicleAdvertisementController as AdminAdController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Admin\BikeController as AdminBikeController;
use App\Http\Controllers\Admin\BikeRentalController as AdminBikeRentalController;
use App\Http\Controllers\Admin\TrackingController as AdminTrackingController;
use App\Http\Controllers\Admin\AnalyticsController;

// Vehicle Owner Controllers
use App\Http\Controllers\VehicleOwner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\VehicleOwner\VehicleController;
use App\Http\Controllers\VehicleOwner\AdvertisementController;
use App\Http\Controllers\VehicleOwner\BookingController as OwnerBookingController;
use App\Http\Controllers\VehicleOwner\EarningsController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\BikeController;
use App\Http\Controllers\User\BikeRentalController;
use App\Http\Controllers\User\BikeRentalPaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ================================
// 1. PUBLIC ROUTES
// ================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Authentication (Breeze)
require __DIR__.'/auth.php';

// ================================
// 2. PAYMENT CALLBACKS (public)
// ================================
Route::get('/bike-rental/payment/return', [BikeRentalPaymentController::class, 'handleReturn'])
    ->name('user.bike-rentals.payment.return');

Route::get('/subscription/callback', [SubscriptionController::class, 'callback'])->name('subscription.callback');

Route::post('/store-redirect', function (Request $request) {
    session(['url.intended' => $request->redirect_to]);
    return response()->json(['success' => true]);
})->name('store.redirect');

// ================================
// 3. AUTHENTICATED USER ROUTES
// ================================
Route::middleware('auth')->group(function () {

    // Dashboard & Profile
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Offer ride flow
    Route::get('/offer-ride', [OfferRideController::class, 'index'])->name('offer.ride');
    Route::get('/become-vehicle-owner', [BecomeVehicleOwnerController::class, 'create'])->name('become.vehicle.owner');
    Route::post('/become-vehicle-owner', [BecomeVehicleOwnerController::class, 'store'])->name('become.vehicle.owner.store');

    // Subscription Routes
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    });

    // ------------------------------
    // RIDE BOOKING
    // ------------------------------
    Route::get('/book/{advertisement}', [UserBookingController::class, 'create'])->name('user.bookings.create');
    Route::post('/book/{advertisement}', [UserBookingController::class, 'store'])->name('user.bookings.store');

    Route::prefix('bookings')->name('user.bookings.')->group(function () {
        Route::get('/', [UserBookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [UserBookingController::class, 'show'])->name('show');
        Route::post('/{booking}/cancel', [UserBookingController::class, 'cancel'])->name('cancel');
        Route::get('/{booking}/payment', [UserBookingController::class, 'payment'])->name('payment');
        Route::post('/{booking}/payment', [UserBookingController::class, 'processPayment'])->name('process-payment');
        Route::match(['GET', 'POST'], '/{booking}/pay', [UserBookingController::class, 'initiatePayment'])->name('payment.initiate');
    });

    // Payment return route
    Route::get('/payment/return', [UserBookingController::class, 'paymentReturn'])->name('payment.return');


// Webhook route - must be POST and public (no auth)
Route::post('/api/bike-rental/webhook', [App\Http\Controllers\User\BookingController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.bike-rental.webhook');
    // Manual verification
    Route::post('/payment/manual-verify', [UserBookingController::class, 'manualVerify'])
        ->name('payment.manual-verify');

    Route::get('/booking/check-subscription/{advertisement}', [UserBookingController::class, 'checkSubscriptionEligibility'])
        ->name('user.bookings.check-subscription');

    // Trip management (user)
    Route::post('/bookings/{booking}/start-trip', [UserBookingController::class, 'startTrip'])->name('user.bookings.start-trip');
    Route::post('/bookings/{booking}/complete-trip', [UserBookingController::class, 'completeTrip'])->name('user.bookings.complete-trip');

    // ------------------------------
    // BIKE RENTAL
    // ------------------------------
    Route::prefix('bikes')->name('user.bikes.')->group(function () {
        Route::get('/', [BikeController::class, 'index'])->name('index');
        Route::get('/{bike}', [BikeController::class, 'show'])->name('show');
        Route::get('/{bike}/rent', [BikeRentalController::class, 'rent'])->name('rent');
        Route::post('/{bike}/rent', [BikeRentalController::class, 'processRent'])->name('rent.process');
    });

    Route::prefix('bike-rentals')->name('user.bike-rentals.')->group(function () {
        Route::get('/', [BikeRentalController::class, 'index'])->name('index');
        Route::get('/{rental}', [BikeRentalController::class, 'show'])->name('show');
        Route::post('/{rental}/cancel', [BikeRentalController::class, 'cancel'])->name('cancel');
        Route::post('/{rental}/return', [BikeRentalController::class, 'returnBike'])->name('return');
        Route::post('/{rental}/pay', [BikeRentalController::class, 'payRental'])->name('pay');
        Route::get('/{rental}/initiate-payment', [BikeRentalController::class, 'initiatePayment'])->name('initiate-payment');
        Route::post('/{rental}/mark-paid', [BikeRentalController::class, 'markAsPaid'])->name('mark-paid');
    });

    // ------------------------------
    // TRACKING
    // ------------------------------
    Route::get('/tracking/ride/{booking}', [TrackingController::class, 'showTracking'])->name('tracking.ride');
    Route::get('/tracking/bike/{rental}', [TrackingController::class, 'showBikeTracking'])->name('tracking.bike');
});

// ================================
// 4. VEHICLE OWNER ROUTES
// ================================
Route::prefix('vehicle-owner')
    ->name('vehicle-owner.')
    ->middleware(['auth', VehicleOwnerMiddleware::class])
    ->group(function () {
        Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('vehicles', VehicleController::class);
        Route::post('vehicles/{vehicle}/toggle-status', [VehicleController::class, 'toggleStatus'])->name('vehicles.toggle-status');
        Route::resource('advertisements', AdvertisementController::class);
        Route::post('advertisements/{advertisement}/duplicate', [AdvertisementController::class, 'duplicate'])->name('advertisements.duplicate');

        Route::post('/advertisements/{advertisement}/start-trip', [AdvertisementController::class, 'startTrip'])->name('advertisements.start-trip');
        Route::post('/advertisements/{advertisement}/complete-trip', [AdvertisementController::class, 'completeTrip'])->name('advertisements.complete-trip');

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [OwnerBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [OwnerBookingController::class, 'show'])->name('show');
            Route::match(['PUT', 'PATCH', 'POST'], '/{booking}', [OwnerBookingController::class, 'update'])->name('update');
            Route::post('/bulk-update', [OwnerBookingController::class, 'bulkUpdate'])->name('bulk-update');
        });

        Route::post('/bookings/{booking}/start-trip', [OwnerBookingController::class, 'startTrip'])->name('bookings.start-trip');
        Route::post('/bookings/{booking}/complete-trip', [OwnerBookingController::class, 'completeTrip'])->name('bookings.complete-trip');

        Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
        Route::post('/withdraw', [EarningsController::class, 'withdraw'])->name('withdraw');
    });

// ================================
// 5. ADMIN ROUTES
// ================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminMiddleware::class])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::get('users/export', [UserController::class, 'export'])->name('users.export');

        Route::resource('vehicles', AdminVehicleController::class)->only(['index', 'show', 'destroy']);
        Route::post('vehicles/{vehicle}/approve', [AdminVehicleController::class, 'approve'])->name('vehicles.approve');
        Route::post('vehicles/{vehicle}/reject', [AdminVehicleController::class, 'reject'])->name('vehicles.reject');

        Route::prefix('advertisements')->name('advertisements.')->group(function () {
            Route::get('/', [AdminAdController::class, 'index'])->name('index');
            Route::get('/{advertisement}', [AdminAdController::class, 'show'])->name('show');
            Route::delete('/{advertisement}', [AdminAdController::class, 'destroy'])->name('destroy');
            Route::post('/{advertisement}/approve', [AdminAdController::class, 'approve'])->name('approve');
            Route::post('/{advertisement}/reject', [AdminAdController::class, 'reject'])->name('reject');
            Route::post('/bulk-approve', [AdminAdController::class, 'bulkApprove'])->name('bulk-approve');
        });

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [AdminBookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
            Route::delete('/{booking}', [AdminBookingController::class, 'destroy'])->name('destroy');
            Route::get('/export', [AdminBookingController::class, 'export'])->name('export');
        });

        Route::resource('bikes', AdminBikeController::class);
        Route::post('bikes/{bike}/update-status', [AdminBikeController::class, 'updateStatus'])->name('bikes.update-status');
        Route::post('bikes/{bike}/generate-qr', [AdminBikeController::class, 'generateQR'])->name('bikes.generate-qr');

        Route::prefix('bike-rentals')->name('bike-rentals.')->group(function () {
            Route::get('/', [AdminBikeRentalController::class, 'index'])->name('index');
            Route::get('/{rental}', [AdminBikeRentalController::class, 'show'])->name('show');
            Route::post('/{rental}/complete', [AdminBikeRentalController::class, 'complete'])->name('complete');
            Route::post('/{rental}/cancel', [AdminBikeRentalController::class, 'cancel'])->name('cancel');
        });

        // Admin Analytics Routes
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [AnalyticsController::class, 'exportRevenue'])->name('analytics.export');

        Route::get('/live-tracking/bikes', [AdminTrackingController::class, 'bikes'])->name('live-tracking.bikes');
        Route::get('/active-bike-rentals', [TrackingController::class, 'getActiveBikeRentals'])->name('active-bike-rentals');
    });

// ================================
// 6. TEST & DEBUG ROUTES (local only)
// ================================

// Webhook route
Route::post('/api/bike-rental/webhook', function() {
    return response()->json(['status' => 'ok'], 200);
})->name('api.bike-rental.webhook');

Route::get('/test-payment-class', function() {
    return class_exists('App\Http\Controllers\PaymentController') ? 'Class exists!' : 'Class not found!';
});

Route::get('/test-bike', function() {
    return 'Bike routes file is loading!';
});

Route::get('/test-ngrok', function () {
    return 'ngrok is working!';
});

if (app()->environment('local')) {
    Route::middleware('auth')->get('/debug-route', function() {
        return [
            'advertisements_create' => route('vehicle-owner.advertisements.create'),
            'current_user_type' => auth()->user()->user_type ?? 'guest'
        ];
    });

    Route::get('/test-sms', function () {
        \App\Helpers\SmsHelper::send('0990179811', 'Test message from Mzuni UNITRAS!');
        return 'SMS sent! Check your phone.';
    });
}