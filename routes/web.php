<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ============================================================
// CONTROLLER IMPORTS
// ============================================================

// Public Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\OfferRideController;
use App\Http\Controllers\BecomeVehicleOwnerController;

// Payment & Payout Controllers
use App\Http\Controllers\PayChanguWebhookController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminPayoutController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\BookingController as UserBookingController;
use App\Http\Controllers\User\BikeController;
use App\Http\Controllers\User\BikeRentalController;
use App\Http\Controllers\User\BikeRentalPaymentController;

// Vehicle Owner Controllers
use App\Http\Controllers\VehicleOwner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\VehicleOwner\VehicleController;
use App\Http\Controllers\VehicleOwner\AdvertisementController;
use App\Http\Controllers\VehicleOwner\BookingController as OwnerBookingController;
use App\Http\Controllers\VehicleOwner\EarningsController;

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

// Bike QR Code Controller
use App\Http\Controllers\BikeQRController;

// Vehicle Vetting Controller
use App\Http\Controllers\Admin\VehicleVettingController;

// ============================================================
// MIDDLEWARE ALIASES
// ============================================================

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VehicleOwnerMiddleware;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// ============================================================
// 1. PUBLIC ROUTES (No Auth Required)
// ============================================================

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Authentication (Breeze)
require __DIR__ . '/auth.php';

// ============================================================
// 2. PAYMENT CALLBACKS & WEBHOOKS (Public)
// ============================================================

// Payment Webhook - PayChangu (No CSRF, No Auth)
Route::post('/payment/webhook', [PayChanguWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('payment.webhook');

// Payment Success/Cancel Callbacks
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
Route::get('/payment/return', [PaymentController::class, 'return'])->name('payment.return');

// Bike Rental Payment Callbacks
Route::get('/bike-rental/payment/return', [BikeRentalPaymentController::class, 'handleReturn'])
    ->name('user.bike-rentals.payment.return');

// Subscription Callback
Route::get('/subscription/callback', [SubscriptionController::class, 'callback'])->name('subscription.callback');

// Store Redirect
Route::post('/store-redirect', function (Request $request) {
    session(['url.intended' => $request->redirect_to]);
    return response()->json(['success' => true]);
})->name('store.redirect');

// Bike Rental Webhook (Public)
Route::post('/api/bike-rental/webhook', [UserBookingController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
    ->name('api.bike-rental.webhook');

// Bike QR Code Public Routes
Route::get('/bike/activate', [BikeQRController::class, 'activate'])->name('bike.activate');
Route::get('/bike/qr/{bike}', [BikeQRController::class, 'getQRCode'])->name('bike.qr');
Route::get('/bike/activate/{qr}', [BikeQRController::class, 'activateFromQR'])->name('bike.activate');

// ============================================================
// 3. AUTHENTICATED USER ROUTES
// ============================================================

Route::middleware('auth')->group(function () {

    // ------------------------------
    // Dashboard & Profile
    // ------------------------------
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------------------
    // Offer Ride Flow
    // ------------------------------
    Route::get('/offer-ride', [OfferRideController::class, 'index'])->name('offer.ride');
    Route::get('/become-vehicle-owner', [BecomeVehicleOwnerController::class, 'create'])->name('become.vehicle.owner');
    Route::post('/become-vehicle-owner', [BecomeVehicleOwnerController::class, 'store'])->name('become.vehicle.owner.store');

    // ------------------------------
    // Subscription
    // ------------------------------
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
    });

    // ------------------------------
    // Ride Booking
    // ------------------------------
    Route::get('/book/{advertisement}', [UserBookingController::class, 'create'])->name('user.bookings.create');
    Route::post('/book/{advertisement}', [UserBookingController::class, 'store'])->name('user.bookings.store');

    Route::prefix('bookings')->name('user.bookings.')->group(function () {
        Route::get('/', [UserBookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [UserBookingController::class, 'show'])->name('show');
        Route::get('/{booking}/payment', [UserBookingController::class, 'payment'])->name('payment');
        Route::post('/{booking}/pay', [UserBookingController::class, 'initiatePayment'])->name('payment.initiate');
        Route::get('/{booking}/payment/return', [UserBookingController::class, 'paymentReturn'])->name('payment.return');
        Route::post('/{booking}/cancel', [UserBookingController::class, 'cancel'])->name('cancel');
        Route::post('/{booking}/start-trip', [UserBookingController::class, 'startTrip'])->name('start-trip');
        Route::post('/{booking}/complete-trip', [UserBookingController::class, 'completeTrip'])->name('complete-trip');
    });

    // Payment Return (Legacy/Alternative)
    Route::get('/payment/return', [UserBookingController::class, 'paymentReturn'])->name('payment.return');

    // Manual Verification
    Route::post('/payment/manual-verify', [UserBookingController::class, 'manualVerify'])
        ->name('payment.manual-verify');

    // Check Subscription Eligibility
    Route::get('/booking/check-subscription/{advertisement}', [UserBookingController::class, 'checkSubscriptionEligibility'])
        ->name('user.bookings.check-subscription');

    // ------------------------------
    // Bike Rental
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
    // Tracking
    // ------------------------------
    Route::get('/tracking/ride/{booking}', [TrackingController::class, 'showTracking'])->name('tracking.ride');
    Route::get('/tracking/bike/{rental}', [TrackingController::class, 'showBikeTracking'])->name('tracking.bike');
});

// ============================================================
// 4. VEHICLE OWNER ROUTES
// ============================================================

Route::prefix('vehicle-owner')
    ->name('vehicle-owner.')
    ->middleware(['auth', VehicleOwnerMiddleware::class])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    // Vehicle Management
    Route::resource('vehicles', VehicleController::class);
    Route::post('vehicles/{vehicle}/toggle-status', [VehicleController::class, 'toggleStatus'])->name('vehicles.toggle-status');

    // Advertisement Management
    Route::resource('advertisements', AdvertisementController::class);
    Route::post('advertisements/{advertisement}/duplicate', [AdvertisementController::class, 'duplicate'])->name('advertisements.duplicate');
    Route::post('advertisements/{advertisement}/start-trip', [AdvertisementController::class, 'startTrip'])->name('advertisements.start-trip');
    Route::post('advertisements/{advertisement}/complete-trip', [AdvertisementController::class, 'completeTrip'])->name('advertisements.complete-trip');

    // Booking Management
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [OwnerBookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [OwnerBookingController::class, 'show'])->name('show');
        Route::match(['PUT', 'PATCH', 'POST'], '/{booking}', [OwnerBookingController::class, 'update'])->name('update');
        Route::post('/bulk-update', [OwnerBookingController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('/{booking}/start-trip', [OwnerBookingController::class, 'startTrip'])->name('start-trip');
        Route::post('/{booking}/complete-trip', [OwnerBookingController::class, 'completeTrip'])->name('complete-trip');
    });

    // Earnings & Withdrawals
    Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings');
    Route::post('/withdraw', [EarningsController::class, 'withdraw'])->name('withdraw');
});

// ============================================================
// 5. PAYOUT ROUTES (Vehicle Owners Only)
// ============================================================

Route::prefix('payout')
    ->name('payout.')
    ->middleware(['auth', VehicleOwnerMiddleware::class])
    ->group(function () {
        Route::get('/dashboard', [PayoutController::class, 'dashboard'])->name('dashboard');
        Route::get('/history', [PayoutController::class, 'history'])->name('history');
        Route::get('/earnings', [PayoutController::class, 'getEarnings'])->name('earnings');
        Route::post('/request', [PayoutController::class, 'requestPayout'])->name('request');
        Route::get('/details', [PayoutController::class, 'getPayoutDetails'])->name('details');
        Route::post('/update-details', [PayoutController::class, 'updatePayoutDetails'])->name('update-details');
        Route::get('/statement', [PayoutController::class, 'statement'])->name('statement');
    });

// ============================================================
// 6. ADMIN ROUTES
// ============================================================

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', AdminMiddleware::class])
    ->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::resource('users', UserController::class)->except(['create', 'store']);
    Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    // Vehicle Management
    Route::resource('vehicles', AdminVehicleController::class)->only(['index', 'show', 'destroy']);
    Route::post('vehicles/{vehicle}/approve', [AdminVehicleController::class, 'approve'])->name('vehicles.approve');
    Route::post('vehicles/{vehicle}/reject', [AdminVehicleController::class, 'reject'])->name('vehicles.reject');

    // Vehicle Vetting Routes
    Route::prefix('vetting')->name('vetting.')->group(function () {
        Route::get('/', [VehicleVettingController::class, 'index'])->name('index');
        Route::get('/{vehicle}', [VehicleVettingController::class, 'show'])->name('show');
        Route::post('/{vehicle}/approve', [VehicleVettingController::class, 'approve'])->name('approve');
        Route::post('/{vehicle}/reject', [VehicleVettingController::class, 'reject'])->name('reject');
        Route::post('/{vehicle}/revet', [VehicleVettingController::class, 'revet'])->name('revet');
        Route::post('/bulk', [VehicleVettingController::class, 'bulkVet'])->name('bulk');
        Route::post('/check-license', [VehicleVettingController::class, 'checkLicense'])->name('check-license');
    });

    // Advertisement Management
    Route::prefix('advertisements')->name('advertisements.')->group(function () {
        Route::get('/', [AdminAdController::class, 'index'])->name('index');
        Route::get('/{advertisement}', [AdminAdController::class, 'show'])->name('show');
        Route::delete('/{advertisement}', [AdminAdController::class, 'destroy'])->name('destroy');
        Route::post('/{advertisement}/approve', [AdminAdController::class, 'approve'])->name('approve');
        Route::post('/{advertisement}/reject', [AdminAdController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [AdminAdController::class, 'bulkApprove'])->name('bulk-approve');
    });

    // Booking Management
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::get('/{booking}', [AdminBookingController::class, 'show'])->name('show');
        Route::delete('/{booking}', [AdminBookingController::class, 'destroy'])->name('destroy');
        Route::get('/export', [AdminBookingController::class, 'export'])->name('export');
    });

    // Bike Management & QR Codes
    Route::prefix('bikes')->name('bikes.')->group(function () {
        // Standard CRUD
        Route::get('/', [AdminBikeController::class, 'index'])->name('index');
        Route::get('/create', [AdminBikeController::class, 'create'])->name('create');
        Route::post('/', [AdminBikeController::class, 'store'])->name('store');
        Route::get('/{bike}', [AdminBikeController::class, 'show'])->name('show');
        Route::get('/{bike}/edit', [AdminBikeController::class, 'edit'])->name('edit');
        Route::put('/{bike}', [AdminBikeController::class, 'update'])->name('update');
        Route::delete('/{bike}', [AdminBikeController::class, 'destroy'])->name('destroy');
        
        // Status Update
        Route::post('/{bike}/update-status', [AdminBikeController::class, 'updateStatus'])->name('update-status');
        
        // QR Code Management
        Route::post('/{bike}/generate-qr', [BikeQRController::class, 'regenerateQR'])->name('generate-qr');
        Route::get('/{bike}/download-qr', [BikeQRController::class, 'downloadQR'])->name('download-qr');
        Route::get('/{bike}/preview-qr', [BikeQRController::class, 'previewQR'])->name('preview-qr');
        Route::post('/bulk-generate-qr', [BikeQRController::class, 'generateAllQRs'])->name('bulk-generate-qr');
        Route::get('/print-labels', [BikeQRController::class, 'printLabels'])->name('print-labels');
    });

    // Bike Rental Management
    Route::prefix('bike-rentals')->name('bike-rentals.')->group(function () {
        Route::get('/', [AdminBikeRentalController::class, 'index'])->name('index');
        Route::get('/{rental}', [AdminBikeRentalController::class, 'show'])->name('show');
        Route::post('/{rental}/complete', [AdminBikeRentalController::class, 'complete'])->name('complete');
        Route::post('/{rental}/cancel', [AdminBikeRentalController::class, 'cancel'])->name('cancel');
    });

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/export', [AnalyticsController::class, 'exportRevenue'])->name('analytics.export');

    // Live Tracking
    Route::get('/live-tracking/bikes', [AdminTrackingController::class, 'bikes'])->name('live-tracking.bikes');
    Route::get('/active-bike-rentals', [TrackingController::class, 'getActiveBikeRentals'])->name('active-bike-rentals');

    // Admin Payout Management
    Route::prefix('payouts')->name('payouts.')->group(function () {
        Route::get('/', [AdminPayoutController::class, 'index'])->name('index');
        Route::get('/{payout}', [AdminPayoutController::class, 'show'])->name('show');
        Route::post('/{payout}/approve', [AdminPayoutController::class, 'approve'])->name('approve');
        Route::post('/{payout}/reject', [AdminPayoutController::class, 'reject'])->name('reject');
        Route::get('/export', [AdminPayoutController::class, 'export'])->name('export');
    });
});

// ============================================================
// 7. TEST & DEBUG ROUTES (Local Only)
// ============================================================

if (app()->environment('local')) {

    Route::get('/test-payment-class', function() {
        return class_exists('App\Http\Controllers\PaymentController') ? 'Class exists!' : 'Class not found!';
    });

    Route::get('/test-bike', function() {
        return 'Bike routes file is loading!';
    });

    Route::get('/test-ngrok', function () {
        return 'ngrok is working!';
    });

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