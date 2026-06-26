<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Transaction;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    /**
     * Rent a bike
     */
    public function store(Request $request, $bikeId)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:30',
            'duration_type' => 'required|in:hour,day',
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $bike = Bike::where('status', 'available')->findOrFail($bikeId);

        // Check subscription
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $totalAmount = $request->duration_type === 'hour'
            ? $bike->price_per_hour * $request->duration
            : $bike->price_per_day * $request->duration;

        $isFree = false;
        $status = 'pending';

        if ($subscription && $subscription->canBookRide()) {
            $isFree = true;
            $totalAmount = 0;
            $status = 'active';
        }

        DB::transaction(function () use ($request, $bike, $totalAmount, $isFree, $status, $subscription, &$rental) {
            $rental = BikeRental::create([
                'rental_code' => 'BIKE-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'bike_id' => $bike->id,
                'duration' => $request->duration,
                'duration_type' => $request->duration_type,
                'rate_per_unit' => $request->duration_type === 'hour' ? $bike->price_per_hour : $bike->price_per_day,
                'subtotal' => $totalAmount,
                'total_amount' => $totalAmount,
                'deposit_paid' => $isFree ? 0 : $bike->deposit_amount,
                'status' => $status,
                'is_paid' => $isFree,
                'pickup_location' => $request->pickup_location,
                'dropoff_location' => $request->dropoff_location,
                'notes' => $request->notes,
                'rental_date' => now(),
                'start_time' => now(),
            ]);

            if ($isFree && $subscription) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'rental_id' => $rental->id,
                    'usage_date' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => $isFree ? 'Rental activated with subscription!' : 'Rental created. Please complete payment.',
            'rental' => $rental,
            'is_free' => $isFree,
            'payment_url' => $isFree ? null : route('payment.initiateRental', $rental),
        ]);
    }

    /**
     * List user's rentals
     */
    public function index()
    {
        $rentals = BikeRental::where('user_id', Auth::id())
            ->with(['bike'])
            ->latest()
            ->paginate(10);

        return response()->json($rentals);
    }

    /**
     * Show a specific rental
     */
    public function show($id)
    {
        $rental = BikeRental::with(['bike'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($rental);
    }

    /**
     * Cancel a rental
     */
    public function cancel($id)
    {
        $rental = BikeRental::where('user_id', Auth::id())->findOrFail($id);

        if ($rental->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This rental cannot be cancelled',
            ], 400);
        }

        $rental->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Rental cancelled successfully',
        ]);
    }

    /**
     * Return a bike (mark as completed)
     */
    public function returnBike($id)
    {
        $rental = BikeRental::where('user_id', Auth::id())->findOrFail($id);

        if ($rental->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Only active rentals can be returned',
            ], 400);
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

        return response()->json([
            'success' => true,
            'message' => 'Bike returned successfully',
        ]);
    }
}