<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paychangu\Laravel\Facades\Paychangu;

class BikeRentalController extends Controller
{
    public function rent(Bike $bike)
    {
        if ($bike->status !== 'available') {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is not available for rent.');
        }
        return view('user.bikes.rent', compact('bike'));
    }

 public function processRent(Request $request, Bike $bike)
{
    // Validate
    $request->validate([
        'duration_type' => 'required|in:hour,day',
        'duration' => 'required|integer|min:1|max:72',
        'start_time' => 'required|date',
        'pickup_location' => 'required|string',
        'dropoff_location' => 'required|string',
        'notes' => 'nullable|string',
    ]);

    // Map duration type to database ENUM values
    $durationTypeMap = [
        'hour' => 'hourly',   // or 'hour' depending on your DB
        'day' => 'daily',      // or 'day' depending on your DB
    ];
    $durationType = $durationTypeMap[$request->duration_type];

    // Calculate total amount
    if ($request->duration_type === 'hour') {
        $ratePerUnit = $bike->price_per_hour;
        $totalAmount = $ratePerUnit * $request->duration;
    } else {
        $ratePerUnit = $bike->price_per_day;
        $totalAmount = $ratePerUnit * $request->duration;
    }

    // Generate unique rental code
    $rentalCode = 'BIKE-' . strtoupper(uniqid());

    // Create rental
    $rental = BikeRental::create([
        'rental_code' => $rentalCode,
        'user_id' => Auth::id(),
        'bike_id' => $bike->id,
        'duration' => $request->duration,
        'duration_type' => $durationType,  // ✅ Use mapped value
        'rate_per_unit' => $ratePerUnit,
        'subtotal' => $totalAmount,
        'total_amount' => $totalAmount,
        'deposit_paid' => $bike->deposit_amount,
        'status' => 'pending',
        'pickup_location' => $request->pickup_location,
        'dropoff_location' => $request->dropoff_location,
        'notes' => $request->notes,
        'start_time' => $request->start_time,
        'rental_date' => now(),
    ]);

    // Create transaction
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

    // Redirect to payment initiation
    return redirect()->route('payment.initiateRental', $rental);
}
    public function index()
    {
        $rentals = BikeRental::where('user_id', Auth::id())
            ->with('bike')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('user.bike-rentals.index', compact('rentals'));
    }

    public function show(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) abort(403);
        return view('user.bike-rentals.show', compact('rental'));
    }

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