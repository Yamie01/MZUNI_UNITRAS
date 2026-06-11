<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paychangu\Laravel\Facades\Paychangu;

class BikeRentalController extends Controller
{
    /**
     * Show bike rental form
     */
    public function rent(Bike $bike)
    {
        if ($bike->status !== 'available') {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is not available for rent.');
        }
        return view('user.bikes.rent', compact('bike'));
    }

    /**
     * Process bike rental
     */
    public function processRent(Request $request, Bike $bike)
{
    // Validate
    $request->validate([
        'duration' => 'required|integer|min:1|max:30',
        'duration_type' => 'required|in:hour,day',
        'pickup_location' => 'required|string',
        'dropoff_location' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    // ✅ Map duration type to database ENUM values
    $durationTypeMap = [
        'hour' => 'hourly',
        'day' => 'daily',
    ];
    $durationType = $durationTypeMap[$request->duration_type];

    // Check for active subscription
    $subscription = Subscription::where('user_id', Auth::id())
        ->where('status', 'active')
        ->where('end_date', '>', now())
        ->first();

    // Calculate total price
    if ($request->duration_type === 'hour') {
        $totalAmount = $bike->price_per_hour * $request->duration;
        $ratePerUnit = $bike->price_per_hour;
    } else {
        $totalAmount = $bike->price_per_day * $request->duration;
        $ratePerUnit = $bike->price_per_day;
    }

    $isFree = false;
    $status = 'pending';

    // Check if subscription covers this rental
    if ($subscription && $subscription->canBookRide()) {
        $isFree = true;
        $totalAmount = 0;
        $status = 'active';
    }

    // Generate rental code
    $rentalCode = 'BIKE-' . strtoupper(uniqid());

    // Create rental
    $rental = BikeRental::create([
        'rental_code' => $rentalCode,
        'user_id' => Auth::id(),
        'bike_id' => $bike->id,
        'duration' => $request->duration,
        'duration_type' => $durationType,  // ✅ Using mapped value
        'rate_per_unit' => $ratePerUnit,
        'subtotal' => $totalAmount,
        'total_amount' => $totalAmount,
        'deposit_paid' => $isFree ? 0 : $bike->deposit_amount,
        'status' => $status,
        'is_paid' => $isFree,
        'pickup_location' => $request->pickup_location,
        'dropoff_location' => $request->dropoff_location,
        'notes' => $request->notes,
        'start_time' => now(),
        'rental_date' => now(),
    ]);

    // If free rental with subscription, record usage and return success
    if ($isFree && $subscription) {
        SubscriptionUsage::create([
            'subscription_id' => $subscription->id,
            'rental_id' => $rental->id,
            'usage_date' => now(),
        ]);
        
        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', "✅ Free rental activated using your {$subscription->type} pass! Bike is ready for pickup.");
    }

    // Create transaction for paid rental
    $txRef = 'RENT-' . $rental->id . '-' . time();
    Transaction::create([
        'reference' => $txRef,
        'transaction_type' => 'bike_rental',
        'transaction_id' => $rental->id,
        'amount' => $totalAmount,
        'platform_fee' => $totalAmount,
        'owner_earnings' => 0,
        'status' => 'pending',
    ]);

    // Redirect to payment
    return redirect()->route('payment.initiateRental', $rental)
        ->with('info', 'Please complete payment to activate your rental.');
    }
    /**
     * Display user's rentals
     */
    public function index()
    {
        $rentals = BikeRental::where('user_id', Auth::id())
            ->with('bike')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('user.bike-rentals.index', compact('rentals'));
    }

    /**
     * Show rental details
     */
    public function show(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }
        return view('user.bike-rentals.show', compact('rental'));
    }

    /**
     * Cancel rental
     */
    public function cancel(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($rental->status !== 'pending') {
            return back()->with('error', 'Cannot cancel this rental.');
        }
        
        $rental->update(['status' => 'cancelled']);
        
        return back()->with('success', 'Rental cancelled successfully.');
    }

    /**
     * Return bike
     */
    public function returnBike(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
            abort(403);
        }
        
        if ($rental->status !== 'active') {
            return back()->with('error', 'Only active rentals can be returned.');
        }
        
        DB::transaction(function () use ($rental) {
            $rental->update([
                'status' => 'completed',
                'actual_return_time' => now(),
            ]);
            if ($rental->bike) {
                $rental->bike->update(['status' => 'available']);
            }
        });
        
        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', 'Bike returned successfully!');
    }
}