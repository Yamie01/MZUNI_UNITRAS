<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Location;
use App\Models\Payment;
use App\Services\PayChanguService;
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
    // 1. SHOW ACTIVATION FORM
    // ============================================================

    /**
     * Show bike activation form.
     */
    public function rent(Bike $bike)
    {
        // Check if bike is available
        if (!$bike->isAvailable()) {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is not available for rent.');
        }

        // Check if user already has an active rental
        $activeRental = BikeRental::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if ($activeRental) {
            return redirect()->route('user.bike-rentals.show', $activeRental)
                ->with('error', 'You already have an active bike rental. Please return it first.');
        }

        $locations = Location::orderBy('name')->get();
        return view('user.bikes.rent', compact('bike', 'locations'));
    }

    // ============================================================
    // 2. ACTIVATE BIKE
    // ============================================================

    /**
     * Activate bike rental with verification.
     */
    public function processRent(Request $request, Bike $bike)
    {
        Log::info('🚲 processRent called', [
            'bike_id' => $bike->id,
            'user_id' => auth()->id(),
            'all' => $request->all()
        ]);

        $user = auth()->user();

        // Check if user already has active rental
        $activeRental = BikeRental::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($activeRental) {
            return back()->with('error', 'You already have an active bike rental. Please return it first.');
        }

        // Validate request
        $request->validate([
            'registration_number' => 'required|string|max:50',
            'phone_number' => 'required|regex:/^[0-9]{10}$/',
            'pickup_location' => 'required|string',
            'dropoff_location' => 'required|string',
        ]);

        // ============================================================
        // 🔐 VERIFY CREDENTIALS - Using User model helper methods
        // ============================================================
        
        // Check if user has valid registration number
        if (!$user->hasValidRegistrationNumber($request->registration_number)) {
            return back()->with('error', '❌ Invalid Registration/Staff ID. Please enter the ID you used when registering.')
                ->withInput();
        }

        // Check if user has valid phone number
        if (!$user->hasValidPhoneNumber($request->phone_number)) {
            return back()->with('error', '❌ Invalid Phone Number. Please enter the phone number you used when registering.')
                ->withInput();
        }

        // Check if bike is still available
        if (!$bike->isAvailable()) {
            return back()->with('error', 'This bike is no longer available.');
        }

        try {
            DB::transaction(function () use ($request, $bike, $user, &$rental) {
                // Generate rental code
                $rentalCode = 'BIKE-' . strtoupper(uniqid());

                // Create rental record
                $rental = BikeRental::create([
                    'rental_code' => $rentalCode,
                    'bike_id' => $bike->id,
                    'user_id' => $user->id,
                    'registration_number' => $request->registration_number,
                    'phone_number' => $request->phone_number,
                    'pickup_location' => $request->pickup_location,
                    'dropoff_location' => $request->dropoff_location,
                    'start_time' => now(),
                    'duration' => 1,
                    'duration_type' => 'hourly',
                    'rate_per_unit' => 2.00,
                    'subtotal' => 0,
                    'total_amount' => 0,
                    'status' => 'active',
                    'is_paid' => false,
                    'rate_per_minute' => 2.00,
                ]);

                Log::info('Rental created', ['rental_id' => $rental->id]);

                // Update bike status to 'rented'
                $bike->update([
                    'status' => 'rented',
                    'current_renter_id' => $user->id,
                ]);

                Log::info('Bike updated', ['bike_id' => $bike->id]);

                // Store in session
                session(['active_rental_id' => $rental->id]);
            });

            Log::info('Transaction completed successfully');
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('success', '🚲 Bike activated successfully! Rate: MWK 2 per minute.');

        } catch (\Exception $e) {
            Log::error('Activation failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to activate bike: ' . $e->getMessage());
        }
    }

    // ============================================================
    // 3. DISPLAY RENTALS
    // ============================================================

    /**
     * List user's bike rentals.
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
     * Show rental details with live timer.
     */
    public function show(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }

        // Refresh session for active rental
        if ($rental->status === 'active') {
            session(['active_rental_id' => $rental->id]);
        }

        return view('user.bike-rentals.show', compact('rental'));
    }

    // ============================================================
    // 4. RETURN BIKE
    // ============================================================

    /**
     * Return bike and calculate total cost.
     */
    public function returnBike(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if (!$rental->isActive()) {
            return back()->with('error', 'This rental is not active.');
        }

        // Calculate total minutes and cost
        $minutes = $rental->elapsed_minutes;
        $totalCost = $minutes * $rental->rate_per_minute;

        DB::transaction(function () use ($rental, $minutes, $totalCost) {
            $rental->update([
                'end_time' => now(),
                'total_minutes' => $minutes,
                'total_amount' => $totalCost,
                'status' => 'completed',
            ]);

            // Update bike status to available
            if ($rental->bike) {
                $rental->bike->markAsAvailable();
            }
        });

        // Clear session
        session()->forget('active_rental_id');

        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', '🚲 Bike returned successfully! Total: ' . $minutes . ' minutes = MWK ' . number_format($totalCost, 2));
    }

    // ============================================================
    // 5. PAYMENT
    // ============================================================

    /**
     * Pay for rental using PayChangu.
     */
    public function initiatePayment(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->is_paid) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'This rental is already paid.');
        }

        if (!$rental->isCompleted()) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'Please return the bike first before paying.');
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
                'description' => "Rental #{$rental->rental_code}",
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

        if ($response['success']) {
            session(['pending_rental_id' => $rental->id]);
            session(['pending_rental_tx_ref' => $txRef]);
            return redirect($response['checkout_url']);
        } else {
            Log::error('PayChangu init failed for rental', $response);
            return back()->with('error', $response['message'] ?? 'Unable to initiate payment');
        }
    }

    /**
     * Mark rental as paid (for testing or manual).
     */
    public function markAsPaid(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->is_paid) {
            return back()->with('error', 'This rental is already paid.');
        }

        DB::transaction(function () use ($rental) {
            Payment::create([
                'bike_rental_id' => $rental->id,
                'user_id' => $rental->user_id,
                'transaction_id' => 'MANUAL-' . time() . '-' . $rental->id,
                'amount' => $rental->total_amount,
                'net_amount' => $rental->total_amount,
                'payment_method' => 'manual',
                'status' => 'completed',
                'payment_date' => now(),
            ]);

            $rental->markAsPaid('manual');
        });

        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', '✅ Payment completed successfully! Amount: MWK ' . number_format($rental->total_amount, 2));
    }

    // ============================================================
    // 6. VERIFICATION HELPERS
    // ============================================================

    /**
     * Check if user has an active rental.
     */
    public function checkActiveRental()
    {
        $activeRental = BikeRental::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return response()->json([
            'has_active' => !is_null($activeRental),
            'rental' => $activeRental
        ]);
    }

    /**
     * Get active rental details for AJAX.
     */
    public function getActiveRental()
    {
        $rental = BikeRental::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('bike')
            ->first();

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'No active rental found.'
            ]);
        }

        return response()->json([
            'success' => true,
            'rental' => [
                'id' => $rental->id,
                'rental_code' => $rental->rental_code,
                'bike' => $rental->bike->brand . ' ' . $rental->bike->model,
                'start_time' => $rental->start_time->format('d M Y, H:i'),
                'elapsed_minutes' => $rental->elapsed_minutes,
                'current_cost' => $rental->current_cost,
                'rate_per_minute' => $rental->rate_per_minute,
            ]
        ]);
    }

    /**
     * Get rental timer status for active rental.
     */
    public function getTimerStatus(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($rental->status !== 'active') {
            return response()->json([
                'active' => false,
                'status' => $rental->status
            ]);
        }

        $minutes = $rental->elapsed_minutes;
        $cost = $rental->current_cost;

        return response()->json([
            'active' => true,
            'rental_id' => $rental->id,
            'minutes' => $minutes,
            'cost' => $cost,
            'formatted_time' => $rental->elapsed_time,
            'formatted_cost' => 'MWK ' . number_format($cost, 2),
        ]);
    }
}