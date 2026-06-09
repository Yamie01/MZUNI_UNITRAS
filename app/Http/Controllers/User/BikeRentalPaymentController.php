<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BikeRental;
use App\Models\Payment;
use App\Services\PayChanguService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BikeRentalPaymentController extends Controller
{
    protected $paychangu;

    public function __construct(PayChanguService $paychangu)
    {
        $this->paychangu = $paychangu;
    }

    /**
     * Show the payment page for a bike rental.
     */
    public function create(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->is_paid) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'This rental has already been paid.');
        }

        return view('user.bike-rentals.payment', compact('rental'));
    }

    /**
     * Initiate a payment with PayChangu.
     */
    public function initiate(Request $request, BikeRental $rental)
    {
        $request->validate([
            'provider' => 'required|in:airtel,tnm',
            'phone_number' => 'required|regex:/^[0-9]{10}$/',
        ]);

        if ($rental->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized access.');
        }

        if ($rental->is_paid) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'This rental has already been paid.');
        }

        $phoneNumber = $request->phone_number;
        if (strlen($phoneNumber) === 10) {
            $phoneNumber = '265' . ltrim($phoneNumber, '0');
        }

        $txRef = 'BIKE-' . $rental->id . '-' . time();
        $provider = $request->provider === 'airtel' ? 'airtel_money' : 'tnm_mpamba';

        $paymentData = [
            'amount'      => (float) $rental->total_amount,
            'currency'    => 'MWK',
            'email'       => auth()->user()->email,
            'phone'       => $phoneNumber,
            'provider'    => $provider,
            'tx_ref'      => $txRef,
            'callback_url' => route('user.bike-rentals.webhook'),
            'return_url'   => route('user.bike-rentals.payment.return'),
            'meta' => [
                'rental_id' => $rental->id,
                'user_id'   => auth()->id(),
                'type'      => 'bike_rental',
            ],
        ];

        Log::info('Initiating bike rental payment', [
            'rental_id' => $rental->id,
            'phone'     => $phoneNumber,
            'amount'    => $rental->total_amount,
        ]);

        $response = $this->paychangu->initializePayment($paymentData);

        if ($response['success']) {
            session(['pending_bike_rental_id' => $rental->id]);
            session(['pending_bike_tx_ref' => $txRef]);

            return redirect($response['checkout_url']);
        }

        return back()->with('error', $response['message']);
    }

    /**
     * Handle the return from PayChangu (customer comes back to your site).
     */
    public function handleReturn(Request $request)
    {
        $rentalId = session('pending_bike_rental_id');
        if (!$rentalId) {
            return redirect()->route('user.bike-rentals.index')
                ->with('error', 'Payment session expired.');
        }

        $rental = BikeRental::find($rentalId);
        if (!$rental) {
            return redirect()->route('user.bike-rentals.index')
                ->with('error', 'Rental not found.');
        }

        $reference = $request->query('reference');
        if ($reference) {
            $verification = $this->paychangu->verifyTransaction($reference);
            if ($verification['success'] && $verification['status'] === 'paid') {
                return $this->processSuccessfulPayment($rental, $reference);
            }
        }

        return redirect()->route('user.bike-rentals.payment', $rental)
            ->with('error', 'Payment was not completed. Please try again.');
    }

    /**
     * Unified webhook handler (accepts GET and POST).
     */
    public function handleWebhook(Request $request)
    {
        Log::info('=== BIKE RENTAL WEBHOOK RECEIVED ===');
        Log::info('Method: ' . $request->method());
        Log::info('Full URL: ' . $request->fullUrl());
        Log::info('Headers: ' . json_encode($request->headers->all()));
        Log::info('Payload: ' . json_encode($request->all()));

        if ($request->isMethod('get')) {
            return $this->handleGetWebhook($request);
        }

        return $this->handlePostWebhook($request);
    }

    /**
     * Handle GET webhook (often used by PayChangu redirects).
     */
    protected function handleGetWebhook(Request $request)
    {
        $tx_ref = $request->query('tx_ref');
        $reference = $request->query('reference');

        Log::info('GET webhook', ['tx_ref' => $tx_ref, 'reference' => $reference]);

        if ($tx_ref && preg_match('/BIKE-(\d+)-/', $tx_ref, $matches)) {
            $rentalId = $matches[1];
            Log::info('Extracted rental_id: ' . $rentalId);

            $rental = BikeRental::find($rentalId);
            if ($rental && !$rental->is_paid && $reference) {
                $verification = $this->paychangu->verifyTransaction($reference);
                Log::info('Verification result: ', $verification);
                if ($verification['success'] && $verification['status'] === 'paid') {
                    return $this->processSuccessfulPayment($rental, $reference);
                }
            }
        }

        Log::warning('GET webhook could not process payment');
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Handle POST webhook (server‑to‑server notification).
     */
    protected function handlePostWebhook(Request $request)
    {
        $payload = $request->all();

        // Extract data – try different possible formats
        $reference = null;
        $rentalId = null;
        $status = null;

        if (isset($payload['data']['reference'])) {
            $reference = $payload['data']['reference'];
            $status = $payload['data']['status'] ?? null;
            $rentalId = $payload['data']['meta']['rental_id'] ?? null;
        } elseif (isset($payload['reference'])) {
            $reference = $payload['reference'];
            $status = $payload['status'] ?? null;
            $rentalId = $payload['meta']['rental_id'] ?? null;
        } elseif (isset($payload['tx_ref'])) {
            $reference = $payload['tx_ref'];
            $status = $payload['status'] ?? 'paid';
            if (preg_match('/BIKE-(\d+)-/', $reference, $matches)) {
                $rentalId = $matches[1];
            }
        }

        Log::info('Extracted webhook data:', [
            'reference' => $reference,
            'status'    => $status,
            'rental_id' => $rentalId,
        ]);

        if (in_array($status, ['paid', 'successful', 'success'])) {
            if (!$rentalId) {
                Log::error('POST webhook: No rental ID found.');
                return response()->json(['error' => 'No rental ID'], 400);
            }

            $rental = BikeRental::find($rentalId);
            if (!$rental) {
                Log::error('POST webhook: Rental not found', ['rental_id' => $rentalId]);
                return response()->json(['error' => 'Rental not found'], 404);
            }

            if (!$rental->is_paid) {
                return $this->processSuccessfulPayment($rental, $reference);
            }

            Log::info('POST webhook: Rental already paid', ['rental_id' => $rental->id]);
        } else {
            Log::warning('POST webhook: Payment not successful', ['status' => $status]);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Process a successful payment: create payment record, update rental and bike status.
     */
    protected function processSuccessfulPayment(BikeRental $rental, $transactionId)
    {
        Log::info('Processing successful payment', [
            'rental_id'     => $rental->id,
            'transaction_id' => $transactionId,
        ]);

        DB::transaction(function () use ($rental, $transactionId) {
            // Create the payment record
            Payment::create([
                'bike_rental_id'  => $rental->id,
                'user_id'         => $rental->user_id,
                'transaction_id'  => $transactionId,
                'amount'          => $rental->total_amount,
                'payment_method'  => 'mobile_money',
                'status'          => 'completed',
                'payment_date'    => now(),
            ]);

            // Update rental
            $rental->update([
                'is_paid'       => true,
                'status'        => 'active',
                'payment_date'  => now(),
            ]);

            // Update bike status to "rented"
            if ($rental->bike) {
                $rental->bike->update(['status' => 'rented']);
                Log::info('Bike status updated to rented', [
                    'bike_id'   => $rental->bike->id,
                    'bike_code' => $rental->bike->bike_code ?? 'N/A',
                ]);
            } else {
                Log::error('Bike not found for rental', ['rental_id' => $rental->id]);
            }
        });

        // Clear session data
        session()->forget(['pending_bike_rental_id', 'pending_bike_tx_ref']);

        Log::info('Payment processed successfully', [
            'rental_id'   => $rental->id,
            'status'      => $rental->status,
            'bike_status' => $rental->bike->status ?? 'unknown',
        ]);

        // If this is called via a webhook (POST), return a JSON response
        if (request()->isMethod('post')) {
            return response()->json(['status' => 'success'], 200);
        }

        // Otherwise it's a redirect (GET) – show the rental page
        return redirect()->route('user.bike-rentals.show', $rental)
            ->with('success', 'Payment successful! Your bike rental is confirmed.');
    }

    /**
     * Show a page where the user waits for payment confirmation.
     */
    public function paymentStatus(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.bike-rentals.payment-status', compact('rental'));
    }

    /**
     * AJAX endpoint to check payment status.
     */
    public function checkStatus(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'is_paid'     => (bool) $rental->is_paid,
            'status'      => $rental->status,
            'bike_status' => $rental->bike->status ?? 'unknown',
        ]);
    }
}