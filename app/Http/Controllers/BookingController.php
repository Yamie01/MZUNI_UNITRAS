<?php

namespace App\Http\Controllers;

use App\Models\VehicleAdvertisement;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $paymentService;
    protected $revenueService;

    public function __construct(PaymentService $paymentService, RevenueService $revenueService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
        $this->revenueService = $revenueService;
    }

    /**
     * Search for available ride advertisements.
     */
    public function search(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'date' => 'required|date|after:today',
            'type' => 'nullable|in:ride_share,taxi,bus,bike_share'
        ]);

        $query = VehicleAdvertisement::active();

        if ($request->type) {
            $query->where('ad_type', $request->type);
        }

        $advertisements = $query->where('from_location', 'like', "%{$request->from}%")
            ->where('to_location', 'like', "%{$request->to}%")
            ->whereDate('departure_time', $request->date)
            ->with(['vehicle', 'owner'])
            ->paginate(15);

        return view('bookings.search', compact('advertisements'));
    }

    /**
     * Create a new booking for an advertisement.
     * Now includes 80/20 revenue split (80% to owner, 20% to platform)
     */
    public function book(Request $request, $advertisementId)
    {
        $advertisement = VehicleAdvertisement::findOrFail($advertisementId);
        
        if ($advertisement->status !== 'approved') {
            return redirect()->back()->with('error', 'This advertisement is not available for booking.');
        }

        if ($advertisement->available_seats < $request->seats) {
            return redirect()->back()->with('error', 'Not enough seats available.');
        }

        $request->validate([
            'seats' => 'required|integer|min:1|max:' . $advertisement->available_seats,
            'pickup_point' => 'required|string',
            'dropoff_point' => 'required|string',
            'special_requests' => 'nullable|string'
        ]);

        $totalPrice = $advertisement->price * $request->seats;
        
        // Calculate 80/20 revenue split
        $platformFee = $totalPrice * 0.20;   // 20% platform commission
        $ownerEarnings = $totalPrice * 0.80; // 80% to vehicle owner

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'vehicle_advertisement_id' => $advertisementId,
            'vehicle_id' => $advertisement->vehicle_id,
            'number_of_seats' => $request->seats,
            'total_price' => $totalPrice,
            'platform_fee' => $platformFee,      // NEW
            'owner_earnings' => $ownerEarnings, // NEW
            'is_paid' => false,                  // NEW (if column exists, otherwise remove)
            'pickup_point' => $request->pickup_point,
            'dropoff_point' => $request->dropoff_point,
            'special_requests' => $request->special_requests,
            'booking_time' => now(),
            'status' => 'pending',
            'trip_status' => 'pending'
        ]);

        // Reduce available seats
        $advertisement->available_seats -= $request->seats;
        $advertisement->save();

        // Redirect to payment initiation (your payment route)
        return redirect()->route('payment.initiate', $booking->id);

        // Check if user has active subscription
$subscription = Subscription::where('user_id', auth()->id())
    ->where('status', 'active')
    ->where('end_date', '>', now())
    ->first();

if ($subscription && $subscription->canBookRide()) {
    // Free booking using subscription
    $totalPrice = 0;
    $isSubscriptionBooking = true;
    
    // Record usage
    SubscriptionUsage::create([
        'subscription_id' => $subscription->id,
        'booking_id' => $booking->id,
        'usage_date' => now(),
    ]);
} else {
    // Normal paid booking
    $totalPrice = $advertisement->price * $request->seats;
    $isSubscriptionBooking = false;
}
    }

    public function store(Request $request, VehicleAdvertisement $advertisement)
    {
        // Validate request
        $request->validate([
            'seats' => 'required|integer|min:1|max:' . $advertisement->available_seats,
            'pickup_point' => 'required|string',
            'dropoff_point' => 'required|string',
            'special_requests' => 'nullable|string',
        ]);

        // ✅ Check if user has an active subscription
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $totalPrice = $advertisement->price * $request->seats;
        $isFreeBooking = false;
        $bookingType = 'paid';
        $status = 'pending';

        if ($subscription && $subscription->canBookRide()) {
            // ✅ FREE booking using subscription
            $isFreeBooking = true;
            $totalPrice = 0;
            $bookingType = 'subscription';
            $status = 'confirmed';  // Auto-confirm, no payment needed
        }

        DB::transaction(function () use ($request, $advertisement, $totalPrice, $bookingType, $status, $isFreeBooking, $subscription, &$booking) {
            $booking = Booking::create([
                'booking_reference' => 'BK-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'vehicle_advertisement_id' => $advertisement->id,
                'vehicle_id' => $advertisement->vehicle_id,
                'number_of_seats' => $request->seats,
                'price_per_seat' => $advertisement->price,
                'subtotal' => $totalPrice,
                'total_price' => $totalPrice,
                'platform_fee' => $totalPrice * 0.20,
                'owner_earnings' => $totalPrice * 0.80,
                'is_paid' => $isFreeBooking,
                'status' => $status,
                'pickup_point' => $request->pickup_point,
                'dropoff_point' => $request->dropoff_point,
                'special_requests' => $request->special_requests,
                'booking_time' => now(),
                'trip_status' => 'pending',
                'booking_type' => $bookingType,
            ]);

            // Reduce available seats
            $advertisement->decrement('available_seats', $request->seats);

            // Record subscription usage if free
            if ($isFreeBooking && $subscription) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'booking_id' => $booking->id,
                    'usage_date' => now(),
                ]);
            }
        });

        // ✅ If free booking, redirect to success page
        if ($isFreeBooking && $subscription) {
            $remainingToday = $subscription->getRemainingTodaysRides();
            return redirect()->route('user.bookings.show', $booking)
                ->with('success', "✅ Free booking confirmed using your {$subscription->type} pass! You have {$remainingToday} free ride(s) left today.");
        }

        // ✅ Paid booking - redirect to payment
        return redirect()->route('payment.initiate', $booking)
            ->with('info', 'Please complete payment to confirm your booking.');
    }

    /**
     * Check subscription eligibility via AJAX (for real-time UI updates)
     */
    public function checkSubscriptionEligibility(Request $request, VehicleAdvertisement $advertisement)
    {
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $seats = $request->input('seats', 1);
        $totalPrice = $advertisement->price * $seats;

        if (!$subscription) {
            return response()->json([
                'eligible' => false,
                'reason' => 'No active subscription',
                'total_price' => $totalPrice,
                'requires_payment' => true,
            ]);
        }

        if (!$subscription->canBookRide()) {
            return response()->json([
                'eligible' => false,
                'reason' => 'Daily ride limit reached',
                'total_price' => $totalPrice,
                'limit' => $subscription->getDailyLimit(),
                'used' => $subscription->getTodaysUsageCount(),
                'requires_payment' => true,
            ]);
        }

        return response()->json([
            'eligible' => true,
            'reason' => 'Free with your subscription!',
            'total_price' => 0,
            'subscription_type' => $subscription->type,
            'remaining_today' => $subscription->getRemainingTodaysRides(),
            'requires_payment' => false,
        ]);
    }
    /**
     * Display user's bookings.
     */
    public function myBookings()
    {
        $bookings = Auth::user()->bookings()
            ->with(['advertisement', 'vehicle', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.my-bookings', compact('bookings'));
    }

    /**
     * Cancel a pending booking.
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Cannot cancel booking at this stage.');
        }

        DB::transaction(function () use ($booking) {
            // Return seats to advertisement
            $advertisement = $booking->advertisement;
            $advertisement->available_seats += $booking->number_of_seats;
            $advertisement->save();

            $booking->status = 'cancelled';
            $booking->trip_status = 'cancelled';
            $booking->save();

            // Refund payment if exists
            if ($booking->payment) {
                $this->paymentService->processRefund($booking->payment);
            }
        });

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Complete a trip (for vehicle owners).
     * Now uses platform_fee instead of system_commission.
     */
    public function completeTrip(Booking $booking)
    {
        // Verify ownership
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if trip is in progress
        if ($booking->trip_status !== 'in_progress') {
            return back()->with('error', 'Trip must be in progress to complete.');
        }
        
        DB::transaction(function () use ($booking) {
            // Process revenue split (if not already done by webhook)
            // The platform_fee and owner_earnings should already be set at booking creation.
            // This ensures final accounting.
            if ($booking->platform_fee === null) {
                // Fallback: calculate now if missing (should not happen)
                $booking->platform_fee = $booking->total_price * 0.20;
                $booking->owner_earnings = $booking->total_price * 0.80;
            }
            
            // Update trip status
            $booking->update([
                'trip_status' => 'completed',
                'trip_completed_at' => now(),
                'status' => 'completed',
            ]);
        });
        
        $message = "Trip completed!\n";
        $message .= "Total: MWK " . number_format($booking->total_price, 2) . "\n";
        $message .= "System Commission (20%): MWK " . number_format($booking->platform_fee ?? 0, 2) . "\n";
        $message .= "Your Earnings (80%): MWK " . number_format($booking->owner_earnings ?? 0, 2);
        
        return back()->with('success', $message);
    }

    /**
     * Start a trip (for vehicle owners).
     */
    public function startTrip(Booking $booking)
    {
        // Verify ownership
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if booking is confirmed
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Booking must be confirmed to start trip.');
        }
        
        $booking->update([
            'trip_status' => 'in_progress',
            'trip_started_at' => now(),
        ]);
        
        return back()->with('success', 'Trip started successfully.');
    }

    /**
     * Display booking details.
     */
    public function show($id)
    {
        $booking = Booking::with(['advertisement', 'vehicle', 'payment', 'user'])
            ->findOrFail($id);
        
        // Check authorization
        if ($booking->user_id !== Auth::id() && 
            $booking->advertisement->owner_id !== Auth::id() && 
            Auth::user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
        
        return view('bookings.show', compact('booking'));
    }
}