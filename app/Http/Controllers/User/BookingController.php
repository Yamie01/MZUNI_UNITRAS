<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\VehicleAdvertisement;
use App\Services\PayChanguService;
use App\Services\PaymentService;
use App\Services\RevenueService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $paymentService;
    protected $revenueService;
    protected $paychangu;

    public function __construct(
        PaymentService $paymentService,
        RevenueService $revenueService,
        PayChanguService $paychangu
    ) {
        $this->paymentService = $paymentService;
        $this->revenueService = $revenueService;
        $this->paychangu = $paychangu;
    }

    // ============================================================
    // 1. CREATE & STORE (Booking Form + Submission)
    // ============================================================

    /**
     * Show the booking form for a specific advertisement.
     */
    public function create(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->status !== 'approved' ||
            $advertisement->departure_time < now() ||
            $advertisement->available_seats < 1) {
            return redirect()->route('search')
                ->with('error', 'This ride is no longer available.');
        }

        // Fetch locations for dropdown
        $locations = Location::orderBy('name')->get();

        // Check subscription status
        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        return view('user.bookings.create', compact('advertisement', 'locations', 'subscription'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request, VehicleAdvertisement $advertisement)
    {
        $user = auth()->user();

        // Block if user has unpaid late fee
        if ($user->hasUnpaidLateFee()) {
            return back()->with('error', 'You have an unpaid late fee. Please pay it before booking another ride.');
        }

        // Validate with location IDs
        $request->validate([
            'seats'             => 'required|integer|min:1|max:' . $advertisement->available_seats,
            'from_location_id'  => 'required|exists:locations,id',
            'to_location_id'    => 'required|exists:locations,id',
            'special_requests'  => 'nullable|string',
            'departure_date'    => 'nullable|date',
            'departure_time'    => 'nullable|date_format:H:i',
        ]);

        // Get location names from IDs
        $fromLocation = Location::find($request->from_location_id);
        $toLocation   = Location::find($request->to_location_id);

        $pickup   = $fromLocation ? $fromLocation->name : '';
        $dropoff  = $toLocation ? $toLocation->name : '';

        // Check subscription
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

        // Combine date & time if provided
        $departureDateTime = $advertisement->departure_time;
        if ($request->filled('departure_date') && $request->filled('departure_time')) {
            $departureDateTime = Carbon::parse($request->departure_date . ' ' . $request->departure_time);
        }

        DB::transaction(function () use ($request, $advertisement, $totalPrice, $bookingType, $isSubscriptionBooking, $subscription, $pickup, $dropoff, $departureDateTime, &$booking) {
            $booking = Booking::create([
                'booking_reference'       => 'BK-' . strtoupper(uniqid()),
                'user_id'                 => Auth::id(),
                'vehicle_advertisement_id' => $advertisement->id,
                'vehicle_id'              => $advertisement->vehicle_id,
                'number_of_seats'         => $request->seats,
                'price_per_seat'          => $advertisement->price,
                'subtotal'                => $totalPrice,
                'total_price'             => $totalPrice,
                'platform_fee'            => $totalPrice * 0.20,
                'owner_earnings'          => $totalPrice * 0.80,
                'is_paid'                 => $isSubscriptionBooking,
                'status'                  => $isSubscriptionBooking ? 'confirmed' : 'pending',
                'pickup_point'            => $pickup,
                'dropoff_point'           => $dropoff,
                'special_requests'        => $request->special_requests,
                'booking_time'            => now(),
                'trip_status'             => 'pending',
                'booking_type'            => $bookingType,
                'trip_date'               => $departureDateTime,
            ]);

            // Decrease available seats
            $advertisement->decrement('available_seats', $request->seats);

            // Record subscription usage if applicable
            if ($isSubscriptionBooking && $subscription) {
                SubscriptionUsage::create([
                    'subscription_id' => $subscription->id,
                    'booking_id'      => $booking->id,
                    'usage_date'      => now(),
                ]);
            }
        });

        if ($isSubscriptionBooking && $subscription) {
            $remainingToday = $subscription->getRemainingTodaysRides();
            return redirect()->route('user.bookings.show', $booking)
                ->with('success', "✅ Booking confirmed using your {$subscription->type} pass! You have {$remainingToday} free ride(s) left today.");
        }

        // Redirect to payment page
        return redirect()->route('user.bookings.payment', $booking)
            ->with('info', 'Please complete payment to confirm your booking.');
    }

    // ============================================================
    // 2. DISPLAY BOOKINGS
    // ============================================================

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
     * Display user's bookings (legacy method - redirect to index).
     */
    public function myBookings()
    {
        return redirect()->route('user.bookings.index');
    }

    // ============================================================
    // 3. PAYMENT
    // ============================================================

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

        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking)
                ->with('error', 'This booking has already been paid.');
        }

        return view('user.bookings.payment', compact('booking'));
    }

    /**
     * Initiate payment for a booking via PayChangu.
     */
    public function initiatePayment(Booking $booking)
    {
        Log::info('🔥 initiatePayment called for booking ID: ' . $booking->id);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);
        Log::info('Booking status: ' . $booking->status . ', is_paid: ' . ($booking->is_paid ? 'true' : 'false'));

        if ($booking->user_id !== Auth::id()) {
            Log::error('❌ Unauthorized: User ' . Auth::id() . ' tried to pay for booking ' . $booking->id);
            abort(403, 'Unauthorized access.');
        }

        if ($booking->status !== 'pending') {
            Log::error('❌ Booking not pending: ' . $booking->status);
            return redirect()->route('user.bookings.index')
                ->with('error', 'This booking cannot be paid for.');
        }

        if ($booking->is_paid) {
            Log::error('❌ Booking already paid');
            return redirect()->route('user.bookings.show', $booking)
                ->with('error', 'This booking has already been paid.');
        }

        Log::info('✅ Initiating PayChangu payment for booking ID: ' . $booking->id);

        $txRef = 'BK-' . $booking->id . '-' . time();

        $paymentData = [
            'amount' => (float) $booking->total_price,
            'currency' => 'MWK',
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'tx_ref' => $txRef,
            'return_url' => route('payment.return'),
            'callback_url' => route('api.bike-rental.webhook'),
            'customization' => [
                'title' => 'Mzuni UNITRAS - Ride Booking',
                'description' => "Booking #{$booking->booking_reference}",
            ],
            'meta' => [
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'type' => 'ride_booking',
            ],
        ];

        try {
            $response = $this->paychangu->initializePayment($paymentData);

            if ($response['success']) {
                session([
                    'pending_ride_booking_id' => $booking->id,
                    'pending_ride_tx_ref' => $txRef,
                ]);

                Log::info('✅ PayChangu payment initiated, redirecting to checkout');
                return redirect($response['checkout_url']);
            } else {
                Log::error('❌ PayChangu payment initiation failed', ['response' => $response]);
                return redirect()->route('user.bookings.payment', $booking)
                    ->with('error', $response['message'] ?? 'Unable to initiate payment. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('❌ PayChangu payment error: ' . $e->getMessage());
            return redirect()->route('user.bookings.payment', $booking)
                ->with('error', 'Payment service error. Please try again.');
        }
    }

    /**
     * Handle payment return from PayChangu.
     */
    public function paymentReturn(Request $request)
    {
        $bookingId = session('pending_ride_booking_id');
        $reference = $request->query('reference') ?? $request->query('tx_ref');

        if (!$bookingId) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Payment session expired.');
        }

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('user.bookings.index')
                ->with('error', 'Booking not found.');
        }

        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking)
                ->with('success', 'Payment already completed!');
        }

        if ($reference) {
            try {
                $verification = $this->paychangu->verifyTransaction($reference);

                if ($verification['success'] && $verification['status'] === 'paid') {
                    return $this->processSuccessfulPayment($booking, $reference);
                }
            } catch (\Exception $e) {
                Log::error('Payment verification error: ' . $e->getMessage());
            }
        }

        return redirect()->route('user.bookings.payment', $booking)
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
 * Handle PayChangu webhook.
 */
/**
 * Handle PayChangu webhook.
 */
public function handleWebhook(Request $request)
{
    Log::info('📨 Webhook received', ['payload' => $request->all()]);

    try {
        $payload = $request->all();
        
        // Get webhook secret from config
        $webhookSecret = config('paychangu.webhook_secret');
        
        // Verify signature if secret is set
        if ($webhookSecret) {
            $signature = $request->header('Signature');
            $computedSignature = hash_hmac('sha256', $request->getContent(), $webhookSecret);
            
            if ($computedSignature !== $signature) {
                Log::warning('❌ Invalid webhook signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        // Extract payment data
        $eventType = $payload['event'] ?? $payload['event_type'] ?? null;
        $paymentData = $payload['data'] ?? $payload;
        
        // Check if it's a successful payment
        if ($eventType === 'charge.completed' || ($paymentData['status'] ?? null) === 'paid') {
            $reference = $paymentData['reference'] ?? $payload['reference'] ?? null;
            $bookingId = $paymentData['meta']['booking_id'] ?? $payload['booking_id'] ?? null;

            if ($bookingId && $reference) {
                $booking = Booking::find($bookingId);
                
                if ($booking && !$booking->is_paid) {
                    // Verify with PayChangu
                    $verification = $this->paychangu->verifyTransaction($reference);
                    
                    if ($verification['success'] && $verification['status'] === 'paid') {
                        return $this->processSuccessfulPayment($booking, $reference);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
    } catch (\Exception $e) {
        Log::error('❌ Webhook error: ' . $e->getMessage());
        return response()->json(['error' => 'Internal server error'], 500);
    }
}    /**
     * Process payment (redirect to payment page).
     */
    public function processPayment(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('user.bookings.index')
                ->with('error', 'This booking cannot be paid for.');
        }

        return redirect()->route('user.bookings.payment', $booking)
            ->with('info', 'Please complete payment for your booking.');
    }

    /**
     * Process successful payment.
     */
    protected function processSuccessfulPayment(Booking $booking, $transactionId)
    {
        DB::transaction(function () use ($booking, $transactionId) {
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'transaction_id' => $transactionId,
                'amount' => $booking->total_price,
                'net_amount' => $booking->total_price, // ← ADDED
                'payment_method' => 'paychangu',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'payment_date' => now(),
            ]);

            if ($booking->advertisement && $booking->advertisement->available_seats > 0) {
                $booking->advertisement->decrement('available_seats', $booking->number_of_seats);
            }
        });

        session()->forget(['pending_ride_booking_id', 'pending_ride_tx_ref']);

        Log::info('✅ Booking payment processed successfully', ['booking_id' => $booking->id]);

        return redirect()->route('user.bookings.show', $booking)
            ->with('success', '✅ Payment successful! Booking confirmed.');
    }

    /**
     * Manually verify payment for a booking.
     */
    public function manualVerify(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::find($request->booking_id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking is not pending.');
        }

        if ($booking->is_paid) {
            return back()->with('error', 'Booking is already paid.');
        }

        DB::transaction(function () use ($booking) {
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'transaction_id' => 'MANUAL-' . time() . '-' . $booking->id,
                'amount' => $booking->total_price,
                'net_amount' => $booking->total_price, // ← ADDED
                'payment_method' => 'manual',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'payment_date' => now(),
            ]);

            if ($booking->advertisement && $booking->advertisement->available_seats > 0) {
                $booking->advertisement->decrement('available_seats', $booking->number_of_seats);
            }
        });

        return redirect()->route('user.bookings.show', $booking)
            ->with('success', '✅ Payment verified manually! Booking confirmed.');
    }

    // ============================================================
    // 4. CANCEL BOOKING
    // ============================================================

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
            $booking->advertisement->increment('available_seats', $booking->number_of_seats);
            $booking->update(['status' => 'cancelled']);
        });

        return redirect()->route('user.bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Legacy cancel method - redirects to the main cancel method.
     */
    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);
        return $this->cancel($booking);
    }

    // ============================================================
    // 5. TRIP MANAGEMENT (For Passengers)
    // ============================================================

    /**
     * Start a trip – passenger boards the vehicle.
     */
    public function startTrip(Booking $booking)
    {
        Log::info('🔥 startTrip called for booking ID: ' . $booking->id);
        Log::info('Current trip_status: ' . $booking->trip_status);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);

        if ($booking->user_id !== Auth::id()) {
            Log::error('❌ Unauthorized: User ' . Auth::id() . ' tried to start booking ' . $booking->id);
            abort(403, 'Unauthorized action.');
        }

        if ($booking->status !== 'confirmed') {
            Log::error('❌ Booking not confirmed: ' . $booking->status);
            return back()->with('error', 'Booking must be confirmed to start trip.');
        }

        if ($booking->trip_status === 'completed') {
            return back()->with('error', 'This trip is already completed.');
        }

        if ($booking->trip_status === 'in_progress') {
            return back()->with('error', 'Trip is already in progress.');
        }

        $booking->update([
            'trip_status' => 'in_progress',
            'trip_started_at' => now(),
        ]);

        Log::info('✅ Trip started successfully for booking ID: ' . $booking->id);

        return back()->with('success', 'You have boarded the vehicle. Safe journey! 🚗');
    }

    /**
     * Complete a trip – passenger reaches destination.
     */
    public function completeTrip(Booking $booking)
    {
        Log::info('🔥 completeTrip called for booking ID: ' . $booking->id);
        Log::info('Current trip_status: ' . $booking->trip_status);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);

        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($booking->trip_status !== 'in_progress') {
            return back()->with('error', 'Trip must be in progress to complete.');
        }

        if ($booking->status === 'completed') {
            return back()->with('error', 'This trip is already completed.');
        }

        DB::transaction(function () use ($booking) {
            if ($booking->platform_fee === null) {
                $booking->platform_fee = $booking->total_price * 0.20;
                $booking->owner_earnings = $booking->total_price * 0.80;
            }

            $booking->update([
                'trip_status' => 'completed',
                'trip_completed_at' => now(),
                'status' => 'completed',
            ]);
        });

        return back()->with('success', 'You have reached your destination. Thank you for riding with us! 🎉');
    }

    // ============================================================
    // 6. SEARCH
    // ============================================================

    /**
     * Search for available ride advertisements.
     */
    public function search(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to'   => 'required|string',
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

    // ============================================================
    // 7. SUBSCRIPTION ELIGIBILITY (AJAX)
    // ============================================================

    /**
     * Check subscription eligibility via AJAX.
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
}