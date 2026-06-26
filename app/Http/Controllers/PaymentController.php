<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\Payment;
use App\Models\Payout;
use App\Services\PayChanguService;
use App\Services\PayChanguPayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paychangu;

    public function __construct(PayChanguService $paychangu)
    {
        $this->paychangu = $paychangu;
    }

    // ============================================================
    // 1. PAYMENT INITIATION
    // ============================================================

    /**
     * Initiate payment for a ride booking.
     */
    public function initiate(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking)
                ->with('error', 'Already paid.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('user.bookings.index')
                ->with('error', 'This booking cannot be paid for.');
        }

        $txRef = 'RIDE-' . $booking->id . '-' . time();

        $paymentData = [
            'amount' => (float) $booking->total_price,
            'currency' => 'MWK',
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'callback_url' => route('api.bike-rental.webhook'),
            'return_url' => route('payment.return'),
            'tx_ref' => $txRef,
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

        Log::info('Initiating PayChangu payment for ride booking', [
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'tx_ref' => $txRef,
        ]);

        $response = $this->paychangu->initializePayment($paymentData);

        if ($response['success']) {
            session(['pending_ride_booking_id' => $booking->id]);
            session(['pending_ride_tx_ref' => $txRef]);
            return redirect($response['checkout_url']);
        } else {
            Log::error('PayChangu init failed', $response);
            return back()->with('error', $response['message'] ?? 'Unable to initiate payment');
        }
    }

    /**
     * Initiate payment for a bike rental.
     */
    public function initiateRental(Request $request, BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->is_paid) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'Already paid.');
        }

        if ($rental->status !== 'pending') {
            return back()->with('error', 'This rental cannot be paid for.');
        }

        $txRef = 'RENT-' . $rental->id . '-' . time();

        $paymentData = [
            'amount' => (float) $rental->total_amount,
            'currency' => 'MWK',
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'callback_url' => route('api.bike-rental.webhook'),
            'return_url' => route('user.bike-rentals.payment.return'),
            'tx_ref' => $txRef,
            'customization' => [
                'title' => 'Mzuni UNITRAS - Bike Rental',
                'description' => "Rental #{$rental->id}",
            ],
            'meta' => [
                'rental_id' => $rental->id,
                'user_id' => auth()->id(),
                'type' => 'bike_rental',
            ],
        ];

        Log::info('Initiating PayChangu payment for bike rental', [
            'rental_id' => $rental->id,
            'amount' => $rental->total_amount,
            'tx_ref' => $txRef,
        ]);

        $response = $this->paychangu->initializePayment($paymentData);

        if ($response['success'] ?? false) {
            session(['pending_rental_id' => $rental->id]);
            session(['pending_rental_tx_ref' => $txRef]);
            return redirect($response['checkout_url']);
        } else {
            return back()->with('error', $response['message'] ?? 'Payment initiation failed');
        }
    }

    /**
     * Initiate payment for mobile app (API-friendly).
     */
    public function initiateMobile(Request $request)
    {
        $request->validate([
            'booking_id' => 'sometimes|exists:bookings,id',
            'rental_id' => 'sometimes|exists:bike_rentals,id',
        ]);

        if ($request->booking_id) {
            $booking = Booking::find($request->booking_id);
            return $this->initiate($booking);
        } elseif ($request->rental_id) {
            $rental = BikeRental::find($request->rental_id);
            return $this->initiateRental($request, $rental);
        }

        return response()->json(['error' => 'No booking or rental ID provided'], 400);
    }

    // ============================================================
    // 2. PAYMENT RETURN & CANCEL
    // ============================================================

    /**
     * Handle payment return (user comes back after payment).
     */
    public function handleReturn(Request $request)
    {
        $bookingId = session('pending_ride_booking_id');
        $rentalId = session('pending_rental_id');
        $reference = $request->query('reference') ?? $request->query('tx_ref');

        if ($bookingId) {
            $booking = Booking::find($bookingId);
            if ($booking) {
                if ($reference) {
                    $verification = $this->paychangu->verifyTransaction($reference);
                    if ($verification['success'] && $verification['status'] === 'paid') {
                        $this->processSuccessfulBookingPayment($booking, $reference);
                        $this->processAutoPayout($booking);
                        return redirect()->route('user.bookings.show', $booking)
                            ->with('success', 'Payment successful! Booking confirmed.');
                    }
                }
                return redirect()->route('user.bookings.payment', $booking)
                    ->with('error', 'Payment was not completed. Please try again.');
            }
        }

        if ($rentalId) {
            $rental = BikeRental::find($rentalId);
            if ($rental) {
                if ($reference) {
                    $verification = $this->paychangu->verifyTransaction($reference);
                    if ($verification['success'] && $verification['status'] === 'paid') {
                        $this->processSuccessfulRentalPayment($rental, $reference);
                        return redirect()->route('user.bike-rentals.show', $rental)
                            ->with('success', 'Payment successful! Rental is active.');
                    }
                }
                return redirect()->route('user.bike-rentals.payment', $rental)
                    ->with('error', 'Payment was not completed. Please try again.');
            }
        }

        return redirect()->route('dashboard')->with('info', 'Payment return received.');
    }

    /**
     * Handle payment cancellation.
     */
    public function handleCancel(Request $request)
    {
        return redirect()->route('dashboard')->with('error', 'You cancelled the payment.');
    }

    // ============================================================
    // 3. WEBHOOK
    // ============================================================

    /**
     * Handle PayChangu webhook.
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            Log::info('Webhook raw payload received', ['payload' => $payload]);

            $signature = $request->header('Signature') ?: $request->header('signature') ?: $request->header('X-Signature');
            $webhookSecret = config('paychangu.webhook_secret');

            if ($webhookSecret) {
                $computedSignature = hash_hmac('sha256', $payload, $webhookSecret);
                if ($computedSignature !== $signature) {
                    Log::warning('Invalid webhook signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }

            $data = $request->all();
            Log::info('Webhook parsed data', $data);

            $eventType = $data['event'] ?? $data['event_type'] ?? null;
            $paymentData = $data['data'] ?? $data;

            if ($eventType === 'charge.completed' || ($paymentData['status'] ?? null) === 'paid') {
                $reference = $paymentData['reference'] ?? $data['reference'] ?? $data['tx_ref'] ?? null;
                $bookingId = $paymentData['meta']['booking_id'] ?? $data['booking_id'] ?? null;
                $rentalId = $paymentData['meta']['rental_id'] ?? $data['rental_id'] ?? null;

                if ($bookingId) {
                    $booking = Booking::find($bookingId);
                    if ($booking && !$booking->is_paid) {
                        $this->processSuccessfulBookingPayment($booking, $reference);
                        $this->processAutoPayout($booking);
                    }
                }

                if ($rentalId) {
                    $rental = BikeRental::find($rentalId);
                    if ($rental && !$rental->is_paid) {
                        $this->processSuccessfulRentalPayment($rental, $reference);
                    }
                }
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook exception: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    // ============================================================
    // 4. MANUAL VERIFICATION (Fallback for Local Testing)
    // ============================================================

    /**
     * Unified manual verification.
     */
    public function manualVerify(Request $request)
    {
        $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'rental_id' => 'nullable|exists:bike_rentals,id',
        ]);

        if ($request->filled('booking_id')) {
            return $this->manualVerifyBooking($request);
        }

        if ($request->filled('rental_id')) {
            return $this->manualVerifyRental($request);
        }

        return back()->with('error', 'No valid booking or rental ID provided.');
    }

    /**
     * Manual verification for booking.
     */
    public function manualVerifyBooking(Request $request)
    {
        $request->validate(['booking_id' => 'required|exists:bookings,id']);
        $booking = Booking::find($request->booking_id);

        if ($booking->user_id !== auth()->id()) {
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
                'net_amount' => $booking->total_price,
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

        $this->processAutoPayout($booking);

        return redirect()->route('user.bookings.index')
            ->with('success', '✅ Booking payment verified manually! Booking is now confirmed.');
    }

    /**
     * Manual verification for rental.
     */
    public function manualVerifyRental(Request $request)
    {
        $request->validate(['rental_id' => 'required|exists:bike_rentals,id']);
        $rental = BikeRental::find($request->rental_id);

        if ($rental->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if ($rental->status !== 'pending') {
            return back()->with('error', 'Rental is not pending.');
        }

        if ($rental->is_paid) {
            return back()->with('error', 'Rental is already paid.');
        }

        DB::transaction(function () use ($rental) {
            Payment::create([
                'booking_id' => null,
                'bike_rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'transaction_id' => 'MANUAL-' . time() . '-' . $rental->id,
                'amount' => $rental->total_amount,
                'net_amount' => $rental->total_amount,
                'payment_method' => 'manual',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $rental->update([
                'is_paid' => true,
                'status' => 'active',
                'payment_date' => now(),
            ]);

            if ($rental->bike) {
                $rental->bike->update(['status' => 'rented']);
            }
        });

        return back()->with('success', '✅ Rental payment verified manually. Bike is now active.');
    }

    // ============================================================
    // 5. INTERNAL HELPERS
    // ============================================================

    /**
     * Process successful booking payment.
     */
    protected function processSuccessfulBookingPayment(Booking $booking, $transactionId)
    {
        DB::transaction(function () use ($booking, $transactionId) {
            Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'transaction_id' => $transactionId,
                'amount' => $booking->total_price,
                'net_amount' => $booking->total_price,
                'payment_method' => 'paychangu',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $booking->update([
                'is_paid' => true,
                'status' => 'confirmed',
                'payment_date' => now(),
            ]);
        });

        session()->forget(['pending_ride_booking_id', 'pending_ride_tx_ref']);
        Log::info('Booking payment processed', ['booking_id' => $booking->id]);
    }

    /**
     * Process successful rental payment.
     */
    protected function processSuccessfulRentalPayment(BikeRental $rental, $transactionId)
    {
        DB::transaction(function () use ($rental, $transactionId) {
            Payment::create([
                'bike_rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'transaction_id' => $transactionId,
                'amount' => $rental->total_amount,
                'net_amount' => $rental->total_amount,
                'payment_method' => 'paychangu',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $rental->update([
                'is_paid' => true,
                'status' => 'active',
                'payment_date' => now(),
            ]);

            if ($rental->bike) {
                $rental->bike->update(['status' => 'rented']);
            }
        });

        session()->forget(['pending_rental_id', 'pending_rental_tx_ref']);
        Log::info('Rental payment processed', ['rental_id' => $rental->id]);
    }

    /**
     * Process auto-payout to vehicle owner.
     */
    protected function processAutoPayout(Booking $booking)
    {
        $owner = $booking->advertisement->owner;
        $ownerEarnings = $booking->owner_earnings ?? ($booking->total_price * 0.80);

        // Skip payout for subscription bookings
        if ($booking->booking_type === 'subscription') {
            Log::info('Subscription booking - skipping payout', ['booking_id' => $booking->id]);
            return;
        }

        if (!$owner->phone) {
            Log::error('Owner has no phone number for payout', ['owner_id' => $owner->id]);
            return;
        }

        $payoutService = new PayChanguPayoutService();
        $reference = 'PAY-' . $booking->id . '-' . time();

        $result = $payoutService->sendMoney(
            $ownerEarnings,
            $owner->phone,
            $reference,
            'Ride earnings from booking #' . $booking->booking_reference
        );

        // Record payout attempt
        Payout::create([
            'booking_id' => $booking->id,
            'user_id' => $owner->id,
            'amount' => $ownerEarnings,
            'platform_fee' => $booking->platform_fee ?? ($booking->total_price * 0.20),
            'recipient_phone' => $owner->phone,
            'provider' => 'airtel_money',
            'reference' => $reference,
            'status' => $result['success'] ? 'completed' : 'failed',
            'response' => json_encode($result),
            'processed_at' => now(),
        ]);

        Log::info('Auto-payout processed', [
            'booking_id' => $booking->id,
            'owner_id' => $owner->id,
            'amount' => $ownerEarnings,
            'success' => $result['success'],
        ]);
    }
}