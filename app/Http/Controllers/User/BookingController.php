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
     * Initiate payment for a booking.
     * This method handles the payment initiation button click.
     */
    public function initiatePayment(Booking $booking)
    {
        // Debug logging
        Log::info('🔥 initiatePayment called for booking ID: ' . $booking->id);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);
        Log::info('Booking status: ' . $booking->status . ', is_paid: ' . ($booking->is_paid ? 'true' : 'false'));

        // Check authorization
        if ($booking->user_id !== Auth::id()) {
            Log::error('❌ Unauthorized: User ' . Auth::id() . ' tried to pay for booking ' . $booking->id);
            abort(403, 'Unauthorized access.');
        }

        // Check if booking can be paid
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

        Log::info('✅ Redirecting to payment initiation for booking ID: ' . $booking->id);

        // Redirect to the payment initiation
        return redirect()->route('payment.initiate', ['booking' => $booking->id]);
    }

    /**
     * Process payment (redirect to PayChangu).
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

        return redirect()->route('payment.initiate', ['booking' => $booking->id]);
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
            // Restore seats
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
        // Debug logging
        Log::info('🔥 startTrip called for booking ID: ' . $booking->id);
        Log::info('Current trip_status: ' . $booking->trip_status);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);

        // Check authorization - passenger can start trip
        if ($booking->user_id !== Auth::id()) {
            Log::error('❌ Unauthorized: User ' . Auth::id() . ' tried to start booking ' . $booking->id);
            abort(403, 'Unauthorized action.');
        }

        // Validate booking status
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

        // Update the booking
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
        // Debug logging
        Log::info('🔥 completeTrip called for booking ID: ' . $booking->id);
        Log::info('Current trip_status: ' . $booking->trip_status);
        Log::info('User ID: ' . Auth::id() . ', Booking user_id: ' . $booking->user_id);

        // Allow passenger to complete trip
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
            // Calculate platform fee and owner earnings if not set
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