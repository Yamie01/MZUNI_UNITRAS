<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\VehicleAdvertisement;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Services\PayChanguService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $paychangu;

    public function __construct(PayChanguService $paychangu)
    {
        $this->paychangu = $paychangu;
        // No middleware call here – routes handle authentication
    }

    /**
     * Display a listing of user's bookings.
     */
    public function index()
    {
        $bookings = Booking::with(['advertisement', 'vehicle', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        
        return view('user.bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(VehicleAdvertisement $advertisement)
    {
        // Check if advertisement is still available
        if ($advertisement->status !== 'approved' || 
            $advertisement->departure_time < now() || 
            $advertisement->available_seats < 1) {
            return redirect()->route('search')
                ->with('error', 'This ride is no longer available.');
        }

        // Check for active subscription (for free booking)
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        return view('user.bookings.create', compact('advertisement', 'subscription'));
    }

    /**
     * Store a newly created booking (with subscription support).
     */
    public function store(Request $request, VehicleAdvertisement $advertisement)
    {
        $request->validate([
            'seats' => 'required|integer|min:1|max:' . $advertisement->available_seats,
            'pickup_point' => 'required|string',
            'dropoff_point' => 'required|string',
            'special_requests' => 'nullable|string',
        ]);

        // Check if user has an active subscription
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $isSubscriptionBooking = false;
        $totalPrice = 0;
        $bookingType = 'paid';

        if ($subscription && $subscription->canBookRide()) {
            // Use subscription (FREE booking)
            $isSubscriptionBooking = true;
            $totalPrice = 0;
            $bookingType = 'subscription';
        } else {
            // No active subscription or limit exceeded → paid booking
            $totalPrice = $advertisement->price * $request->seats;
            $bookingType = 'paid';
        }

        DB::transaction(function () use ($request, $advertisement, $totalPrice, $bookingType, $isSubscriptionBooking, $subscription, &$booking) {
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
                'is_paid' => $isSubscriptionBooking,
                'status' => $isSubscriptionBooking ? 'confirmed' : 'pending',
                'pickup_point' => $request->pickup_point,
                'dropoff_point' => $request->dropoff_point,
                'special_requests' => $request->special_requests,
                'booking_time' => now(),
                'trip_status' => 'pending',
                'booking_type' => $bookingType,
            ]);

            // Reduce available seats
            $advertisement->decrement('available_seats', $request->seats);

            // Record subscription usage if applicable
            if ($isSubscriptionBooking && $subscription) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'booking_id' => $booking->id,
                    'usage_date' => now(),
                ]);
            }
        });

        if ($isSubscriptionBooking && $subscription) {
            $remainingToday = $subscription->getRemainingTodaysRides();
            return redirect()->route('user.bookings.show', $booking)
                ->with('success', "✅ Booking confirmed using your {$subscription->type} pass! You have {$remainingToday} free ride(s) left today.");
        }

        // For paid bookings, redirect to payment
        return redirect()->route('user.bookings.payment', $booking)
            ->with('info', 'Please complete payment to confirm your booking.');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load(['advertisement', 'vehicle', 'payment']);
        
        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'This booking cannot be cancelled.');
        }
        
        DB::transaction(function () use ($booking) {
            // Restore seats
            $booking->advertisement->increment('available_seats', $booking->number_of_seats);
            $booking->update(['status' => 'cancelled']);
        });
        
        return redirect()->route('user.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Show payment page for booking.
     */
    public function payment(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'pending') {
            return redirect()->route('user.bookings.index')
                ->with('error', 'This booking cannot be paid for.');
        }
        
        return view('user.bookings.payment', compact('booking'));
    }

    /**
     * Initialize PayChangu payment for ride booking.
     */
    public function initiatePayment(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'pending') {
            return redirect()->route('user.bookings.index')
                ->with('error', 'This booking cannot be paid for.');
        }
        
        // Create unique transaction reference
        $txRef = 'RIDE-' . $booking->id . '-' . time();
        
        // Prepare payment data
        $paymentData = [
            'amount' => (float) $booking->total_price,
            'currency' => 'MWK',
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'callback_url' => route('payment.webhook'),
            'return_url' => route('payment.return'),
            'tx_ref' => $txRef,
            'customization' => [
                'title' => 'Mzuni UNITRAS - Ride Booking',
                'description' => "Booking #{$booking->booking_reference} - {$booking->advertisement->from_location} to {$booking->advertisement->to_location}",
            ],
            'meta' => [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'type' => 'ride_booking',
            ],
        ];
        
        Log::info('Initiating PayChangu payment for ride booking', [
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'tx_ref' => $txRef,
        ]);
        
        // Initialize payment
        $response = $this->paychangu->initializePayment($paymentData);
        
        if ($response['success']) {
            session(['pending_ride_booking_id' => $booking->id]);
            session(['pending_ride_tx_ref' => $txRef]);
            
            // Redirect to PayChangu checkout page
            return redirect($response['checkout_url']);
        } else {
            return back()->with('error', $response['message'] ?? 'Unable to initiate payment');
        }
    }

    /**
     * Handle payment return (user comes back after payment).
     */
    public function paymentReturn(Request $request)
    {
        $bookingId = session('pending_ride_booking_id');
        
        if (!$bookingId) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Payment session expired.');
        }
        
        $booking = Booking::find($bookingId);
        
        if (!$booking) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Booking not found.');
        }
        
        $reference = $request->query('reference');
        
        if ($reference) {
            // Verify the transaction
            $verification = $this->paychangu->verifyTransaction($reference);
            
            if ($verification['success'] && $verification['status'] === 'paid') {
                return $this->processSuccessfulPayment($booking, $reference);
            }
        }
        
        return redirect()->route('user.bookings.payment', $booking)
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Handle PayChangu webhook for ride bookings.
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        
        Log::info('PayChangu webhook received for ride booking', ['payload' => $payload]);
        
        $eventType = $payload['event'] ?? $payload['event_type'] ?? null;
        $paymentData = $payload['data'] ?? $payload;
        
        if ($eventType === 'charge.completed' || ($paymentData['status'] ?? null) === 'paid') {
            $reference = $paymentData['reference'] ?? $payload['reference'] ?? null;
            $bookingId = $paymentData['meta']['booking_id'] ?? $payload['booking_id'] ?? null;
            
            if (!$reference || !$bookingId) {
                Log::error('Webhook missing reference or booking_id');
                return response()->json(['error' => 'Missing data'], 400);
            }
            
            $booking = Booking::find($bookingId);
            
            if (!$booking) {
                Log::error('Webhook booking not found', ['booking_id' => $bookingId]);
                return response()->json(['error' => 'Booking not found'], 404);
            }
            
            if (!$booking->is_paid) {
                $this->processSuccessfulPayment($booking, $reference);
            }
        }
        
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Process successful payment for ride booking.
     */
    protected function processSuccessfulPayment(Booking $booking, $transactionId)
    {
        DB::transaction(function () use ($booking, $transactionId) {
            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'transaction_id' => $transactionId,
                'amount' => $booking->total_price,
                'payment_method' => 'paychangu',
                'status' => 'completed',
                'payment_date' => now(),
            ]);
            
            // Update booking status
            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'payment_date' => now(),
            ]);
        });
        
        // Clear session
        session()->forget(['pending_ride_booking_id', 'pending_ride_tx_ref']);
        
        Log::info('Ride booking payment processed successfully', [
            'booking_id' => $booking->id,
            'transaction_id' => $transactionId,
        ]);
        
        return redirect()->route('user.bookings.show', $booking)
            ->with('success', '✅ Payment successful! Your booking is confirmed. Transaction ID: ' . $transactionId);
    }

    /**
     * Check if user can book using subscription (AJAX endpoint).
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
            ]);
        }

        if (!$subscription->canBookRide()) {
            return response()->json([
                'eligible' => false,
                'reason' => 'Daily ride limit reached',
                'total_price' => $totalPrice,
                'limit' => $subscription->getDailyLimit(),
                'used' => $subscription->getTodaysUsageCount(),
            ]);
        }

        return response()->json([
            'eligible' => true,
            'reason' => 'Subscription applicable',
            'total_price' => 0,
            'subscription_type' => $subscription->type,
            'remaining_today' => $subscription->getRemainingTodaysRides(),
        ]);
    }
}