<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Services\PayChanguService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BikeRentalController extends Controller
{
    protected $paychangu;

    public function __construct(PayChanguService $paychangu)
    {
        $this->paychangu = $paychangu;
    }

    // ============================================================
    // 1. RENTAL FORM & PROCESSING
    // ============================================================

    /**
     * Show bike rental form.
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
     * Process bike rental.
     */
    public function processRent(Request $request, Bike $bike)
    {
        $request->validate([
            'duration' => 'required|integer|min:1|max:30',
            'duration_type' => 'required|in:hour,day',
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $duration = (int) $request->duration;
        $durationType = $request->duration_type;

        $durationTypeMap = ['hour' => 'hourly', 'day' => 'daily'];
        $dbDurationType = $durationTypeMap[$durationType] ?? 'hourly';

        $subscription = Subscription::where('user_id', Auth::id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        $rate = $durationType === 'hour' ? $bike->price_per_hour : $bike->price_per_day;
        $totalAmount = $rate * $duration;

        $isFree = false;
        $status = 'pending';
        $deposit = $bike->deposit_amount;

        if ($subscription && $subscription->canBookRide()) {
            $isFree = true;
            $totalAmount = 0;
            $status = 'active';
            $deposit = 0;
        }

        $rentalCode = 'BIKE-' . strtoupper(uniqid());
        $hoursToAdd = $durationType === 'hour' ? $duration : $duration * 24;

        $rental = BikeRental::create([
            'rental_code' => $rentalCode,
            'user_id' => Auth::id(),
            'bike_id' => $bike->id,
            'duration' => $duration,
            'duration_type' => $dbDurationType,
            'rate_per_unit' => $rate,
            'subtotal' => $totalAmount,
            'total_amount' => $totalAmount,
            'deposit_paid' => $deposit,
            'status' => $status,
            'is_paid' => $isFree,
            'pickup_location' => $request->pickup_location,
            'dropoff_location' => $request->dropoff_location,
            'notes' => $request->notes,
            'start_time' => now(),
            'rental_date' => now(),
            'expected_return_time' => now()->addHours($hoursToAdd),
        ]);

        if ($isFree && $subscription) {
            SubscriptionUsage::create([
                'subscription_id' => $subscription->id,
                'rental_id' => $rental->id,
                'usage_date' => now(),
            ]);
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('success', "✅ Free rental activated using your {$subscription->type} pass!");
        }

        return redirect()->route('user.bike-rentals.payment', $rental)
            ->with('info', 'Please complete payment to activate your rental.');
    }

    // ============================================================
    // 2. DISPLAY RENTALS
    // ============================================================

    /**
     * List user's rentals.
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
     * Show rental details.
     */
    public function show(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }
        return view('user.bike-rentals.show', compact('rental'));
    }

    // ============================================================
    // 3. CANCEL RENTAL
    // ============================================================

    /**
     * Cancel a pending rental.
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

    // ============================================================
    // 4. RETURN BIKE (with Late Fee)
    // ============================================================

    /**
     * Return a bike (includes late fee calculation).
     */
    public function returnBike(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id() && Auth::user()->user_type !== 'admin') {
            abort(403);
        }

        if ($rental->status !== 'active' && $rental->status !== 'rented') {
            return back()->with('error', 'Only active rentals can be returned.');
        }

        DB::transaction(function () use ($rental) {
            $rental->actual_return_time = now();

            // Check for late return
            if ($rental->expected_return_time && now()->gt($rental->expected_return_time)) {
                $hoursLate = now()->diffInHours($rental->expected_return_time);
                $lateFee = $hoursLate * 500; // Example: MWK 500 per hour
                $rental->late_fee = $lateFee;
                $rental->late_fee_paid = false;
                $rental->status = 'returned_late';
            } else {
                $rental->status = 'completed';
            }

            $rental->save();

            // Make bike available again
            if ($rental->bike) {
                $rental->bike->update(['status' => 'available']);
            }
        });

        // If late fee, redirect to payment
        if ($rental->late_fee > 0 && !$rental->late_fee_paid) {
            return redirect()->route('rentals.pay-late-fee', $rental->id)
                ->with('info', 'You have a late fee of MWK ' . number_format($rental->late_fee, 0) . '. Please pay it.');
        }

        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', 'Bike returned on time. Thank you!');
    }

    // ============================================================
    // 5. LATE FEE PAYMENT
    // ============================================================

    /**
     * Show late fee payment page.
     */
    public function payLateFee($rentalId)
    {
        $rental = BikeRental::findOrFail($rentalId);
        if ($rental->late_fee_paid) {
            return redirect()->route('dashboard')->with('error', 'Already paid.');
        }
        return view('rentals.pay-late-fee', compact('rental'));
    }

    /**
     * Initiate late fee payment.
     */
    public function initiateLateFeePayment(Request $request, $rentalId)
    {
        $rental = BikeRental::findOrFail($rentalId);

        $txRef = 'LATE-' . $rental->id . '-' . time();

        $paymentData = [
            'amount' => (float) $rental->late_fee,
            'currency' => 'MWK',
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'callback_url' => route('rentals.late-fee-callback'),
            'return_url' => route('rentals.late-fee-success'),
            'tx_ref' => $txRef,
            'customization' => [
                'title' => 'Mzuni UNITRAS - Late Fee',
                'description' => "Late fee for rental #{$rental->id}",
            ],
            'meta' => [
                'rental_id' => $rental->id,
                'type' => 'late_fee',
            ],
        ];

        $response = $this->paychangu->initializePayment($paymentData);

        if ($response['success'] ?? false) {
            session(['pending_late_fee_rental_id' => $rental->id]);
            return redirect($response['checkout_url']);
        } else {
            return back()->with('error', $response['message'] ?? 'Payment initiation failed.');
        }
    }

    /**
     * Late fee payment callback (webhook).
     */
    public function lateFeeCallback(Request $request)
    {
        $payload = $request->all();
        Log::info('Late fee callback received', $payload);

        $rentalId = $payload['rental_id'] ?? $payload['meta']['rental_id'] ?? null;
        if ($rentalId) {
            $rental = BikeRental::find($rentalId);
            if ($rental && !$rental->late_fee_paid) {
                $rental->update([
                    'late_fee_paid' => true,
                    'status' => 'completed',
                ]);
                Log::info('Late fee paid for rental', ['rental_id' => $rentalId]);
            }
        }
        return response()->json(['status' => 'ok'], 200);
    }
}