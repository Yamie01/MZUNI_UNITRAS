<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleAdvertisement;
use App\Models\Booking;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Book a ride
     */
    public function store(Request $request, $rideId)
    {
        $request->validate([
            'seats' => 'required|integer|min:1',
            'pickup_point' => 'required|string',
            'dropoff_point' => 'required|string',
            'special_requests' => 'nullable|string',
        ]);

        $ride = VehicleAdvertisement::findOrFail($rideId);

        if ($ride->available_seats < $request->seats) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough seats available',
            ], 400);
        }

        // Check subscription
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $totalPrice = $ride->price * $request->seats;
        $isFree = false;
        $status = 'pending';

        if ($subscription && $subscription->canBookRide()) {
            $isFree = true;
            $totalPrice = 0;
            $status = 'confirmed';
        }

        DB::transaction(function () use ($request, $ride, $totalPrice, $isFree, $status, $subscription, &$booking) {
            $booking = Booking::create([
                'booking_reference' => 'BK-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'vehicle_advertisement_id' => $ride->id,
                'vehicle_id' => $ride->vehicle_id,
                'number_of_seats' => $request->seats,
                'price_per_seat' => $ride->price,
                'subtotal' => $ride->price * $request->seats,
                'total_price' => $totalPrice,
                'is_paid' => $isFree,
                'status' => $status,
                'pickup_point' => $request->pickup_point,
                'dropoff_point' => $request->dropoff_point,
                'special_requests' => $request->special_requests,
                'booking_type' => $isFree ? 'subscription' : 'paid',
                'booking_time' => now(),
                'trip_status' => 'pending',
            ]);

            $ride->decrement('available_seats', $request->seats);

            if ($isFree && $subscription) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'booking_id' => $booking->id,
                    'usage_date' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => $isFree ? 'Booking confirmed with subscription!' : 'Booking created. Please complete payment.',
            'booking' => $booking,
            'is_free' => $isFree,
            'payment_url' => $isFree ? null : route('payment.initiate', $booking),
        ]);
    }

    /**
     * List user's bookings
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['advertisement', 'vehicle'])
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    /**
     * Show a specific booking
     */
    public function show($id)
    {
        $booking = Booking::with(['advertisement', 'vehicle', 'payment'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($booking);
    }

    /**
     * Cancel a booking
     */
    public function cancel($id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled',
            ], 400);
        }

        DB::transaction(function () use ($booking) {
            $booking->advertisement->increment('available_seats', $booking->number_of_seats);
            $booking->update(['status' => 'cancelled']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
        ]);
    }
}