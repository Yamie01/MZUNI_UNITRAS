<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paychangu\Laravel\Facades\Paychangu;

class PaymentController extends Controller
{
    /**
     * Initiate payment for a ride booking (80% owner, 20% platform)
     */
    public function initiate(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) abort(403);
        if ($booking->is_paid) {
            return redirect()->route('user.bookings.show', $booking)->with('error', 'Already paid.');
        }

        $totalAmount = $booking->total_price;
        $txRef = 'TXN-BOOK-' . $booking->id . '-' . time();

        $transaction = Transaction::updateOrCreate(
            ['transaction_id' => $booking->id, 'transaction_type' => 'booking'],
            [
                'reference' => $txRef,
                'amount' => $totalAmount,
                'platform_fee' => $totalAmount * 0.20,
                'owner_earnings' => $totalAmount * 0.80,
                'status' => 'pending',
            ]
        );

        try {
            $response = Paychangu::create_checkout_link([
                'amount' => $totalAmount,
                'email' => auth()->user()->email,
                'first_name' => auth()->user()->name,
                'last_name' => '',
                'currency' => 'MWK',
                'return_url' => url('/payment/return'),
                'callback_url' => url('/api/bike-rental/webhook'),
                'meta' => [
                    'transaction_id' => $transaction->id,
                    'booking_id' => $booking->id,
                    'user_id' => auth()->id(),
                    'tx_ref' => $txRef,
                    'payment_type' => 'ride_booking',
                ],
            ]);

            if ($response['success']) {
                return redirect($response['checkout_url']);
            }

            return back()->with('error', 'Unable to initiate payment.');
        } catch (\Exception $e) {
            Log::error('Payment initiation error: ' . $e->getMessage());
            return back()->with('error', 'Payment service error.');
        }
    }

    /**
     * Initiate payment for a bike rental
     */
    public function initiateRental(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }

        if ($rental->is_paid) {
            return redirect()->route('user.bike-rentals.show', $rental)
                ->with('error', 'Already paid.');
        }

        $totalAmount = $rental->total_amount;
        $txRef = 'RENT-' . $rental->id . '-' . time();

        $transaction = Transaction::updateOrCreate(
            ['transaction_id' => $rental->id, 'transaction_type' => 'bike_rental'],
            [
                'reference' => $txRef,
                'amount' => $totalAmount,
                'platform_fee' => $totalAmount,
                'owner_earnings' => 0,
                'status' => 'pending',
            ]
        );

        try {
            $response = Paychangu::create_checkout_link([
                'amount' => $totalAmount,
                'email' => auth()->user()->email,
                'first_name' => auth()->user()->name,
                'last_name' => '',
                'currency' => 'MWK',
                'return_url' => url('/payment/return'), // fixed: not /api/payment/return
                'callback_url' => url('/api/bike-rental/webhook'),
                'meta' => [
                    'transaction_id' => $transaction->id,
                    'rental_id' => $rental->id,
                    'user_id' => auth()->id(),
                    'tx_ref' => $txRef,
                    'payment_type' => 'bike_rental',
                ],
            ]);

            if ($response['success']) {
                return redirect($response['checkout_url']);
            }

            return back()->with('error', 'Unable to initiate payment: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Rental payment error: ' . $e->getMessage());
            return back()->with('error', 'Payment service error: ' . $e->getMessage());
        }
    }

    /**
 * Initiate payment for mobile app
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
    } else {
        $rental = BikeRental::find($request->rental_id);
        return $this->initiateRental($rental);
    }
    }

    /**
     * Return URL – user comes back after payment (browser redirect)
     */
    public function handleReturn(Request $request)
    {
        $tx_ref = $request->query('tx_ref');
        $status = $request->query('status');

        Log::info('Return URL called', ['tx_ref' => $tx_ref, 'status' => $status]);

        if (!$tx_ref || $status !== 'success') {
            return redirect()->route('dashboard')->with('error', 'Payment was not completed.');
        }

        $transaction = Transaction::where('reference', $tx_ref)->first();
        if (!$transaction) {
            Log::error('Transaction not found in return', ['tx_ref' => $tx_ref]);
            return redirect()->route('dashboard')->with('error', 'Transaction not found.');
        }

        if ($transaction->status === 'completed') {
            return $this->redirectToSuccess($transaction);
        }

        // Verify with PayChangu API
        $verification = Paychangu::verify_checkout($tx_ref);
        if ($verification['success'] && ($verification['data']['status'] ?? '') === 'success') {
            DB::transaction(function () use ($transaction, $verification) {
                $transaction->update([
                    'status' => 'completed',
                    'payment_details' => json_encode($verification['data']['authorization'] ?? []),
                    'paid_at' => now(),
                ]);
                $this->updateRelatedModel($transaction);
            });
            return $this->redirectToSuccess($transaction);
        }

        return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
    }

    /**
     * Manual verification for ride bookings (fallback)
     */
    public function manualVerifyBooking(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return back()->with('error', 'Booking not found.');
        }

        $transaction = Transaction::where('transaction_id', $booking->id)
            ->where('transaction_type', 'booking')
            ->first();

        if (!$transaction) {
            return back()->with('error', 'No payment transaction found.');
        }

        if ($booking->is_paid) {
            return back()->with('info', 'Booking is already paid.');
        }

        try {
            $verification = Paychangu::verify_checkout($transaction->reference);
            if ($verification['success'] && ($verification['data']['status'] ?? '') === 'success') {
                DB::transaction(function () use ($booking, $transaction, $verification) {
                    $transaction->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                    ]);
                    $booking->update([
                        'is_paid' => true,
                        'status' => 'confirmed',
                        'payment_date' => now(),
                    ]);
                });
                return back()->with('success', 'Payment confirmed! Booking is now confirmed.');
            }
            return back()->with('error', 'Payment not confirmed.');
        } catch (\Exception $e) {
            Log::error('Manual booking verification error: ' . $e->getMessage());
            return back()->with('error', 'Verification failed.');
        }
    }

    /**
     * Manual verification for bike rentals (fallback)
     */
    public function manualVerify(Request $request)
    {
        $rentalId = $request->input('rental_id');
        $rental = BikeRental::find($rentalId);

        if (!$rental) {
            return back()->with('error', 'Rental not found.');
        }

        $transaction = Transaction::where('transaction_id', $rental->id)
            ->where('transaction_type', 'bike_rental')
            ->first();

        if (!$transaction) {
            return back()->with('error', 'No payment transaction found.');
        }

        if ($rental->is_paid) {
            return back()->with('info', 'Rental is already active.');
        }

        try {
            $verification = Paychangu::verify_checkout($transaction->reference);
            if ($verification['success'] && ($verification['data']['status'] ?? '') === 'success') {
                DB::transaction(function () use ($rental, $transaction, $verification) {
                    $transaction->update([
                        'status' => 'completed',
                        'paid_at' => now(),
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
                return back()->with('success', 'Payment confirmed! Rental is now active.');
            } else {
                return back()->with('error', 'Payment not confirmed. Status: ' . ($verification['data']['status'] ?? 'unknown'));
            }
        } catch (\Exception $e) {
            Log::error('Manual verification error: ' . $e->getMessage());
            return back()->with('error', 'Verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Redirect to the appropriate success page
     */
    private function redirectToSuccess($transaction)
    {
        if ($transaction->transaction_type === 'booking') {
            return redirect()->route('user.bookings.show', $transaction->transaction_id)
                ->with('success', 'Payment successful! Your booking is confirmed.');
        }
        return redirect()->route('user.bike-rentals.show', $transaction->transaction_id)
            ->with('success', 'Payment successful! Your rental is active.');
    }

    /**
     * Update the related model after successful payment
     */
    private function updateRelatedModel(Transaction $transaction)
    {
        Log::info('updateRelatedModel called', [
            'transaction_id' => $transaction->id,
            'transaction_type' => $transaction->transaction_type,
            'transaction_id_field' => $transaction->transaction_id,
        ]);

        if ($transaction->transaction_type === 'booking') {
            $booking = Booking::find($transaction->transaction_id);
            Log::info('Booking found?', ['found' => $booking ? 'yes' : 'no']);
            if ($booking && !$booking->is_paid) {
                $booking->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_date' => now(),
                ]);
                Log::info('Booking updated', ['booking_id' => $booking->id]);
            }
        } elseif ($transaction->transaction_type === 'bike_rental') {
            $rental = BikeRental::find($transaction->transaction_id);
            Log::info('Rental found?', ['found' => $rental ? 'yes' : 'no']);
            if ($rental && !$rental->is_paid) {
                $rental->update([
                    'is_paid' => true,
                    'status' => 'active',
                    'payment_date' => now(),
                ]);
                if ($rental->bike) {
                    $rental->bike->update(['status' => 'rented']);
                    Log::info('Bike updated to rented', ['bike_id' => $rental->bike_id]);
                }
                Log::info('Rental updated', ['rental_id' => $rental->id]);
            }
        }
    }

    /**
     * Cancel URL – user cancelled the payment
     */
    public function handleCancel(Request $request)
    {
        return redirect()->route('dashboard')->with('error', 'You cancelled the payment.');
    }

    /**
     * Webhook handler – PayChangu calls this server-to-server
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            Log::info('Webhook raw payload', ['payload' => $payload]);

            // Try multiple header names (case‑insensitive fallback)
            $signature = $request->header('Signature') ?: $request->header('signature') ?: $request->header('X-Signature');
            $webhookSecret = config('paychangu.webhook_secret');

            if (!$webhookSecret) {
                Log::error('Webhook secret is missing in config');
                return response()->json(['error' => 'Webhook secret missing'], 500);
            }

            $computedSignature = hash_hmac('sha256', $payload, $webhookSecret);
            if ($computedSignature !== $signature) {
                Log::warning('Invalid webhook signature', [
                    'received' => $signature,
                    'computed' => $computedSignature,
                    'secret' => $webhookSecret,
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            $data = $request->all();
            Log::info('Webhook parsed data', $data);

            // Check both event_type and status, then use tx_ref first
            if (($data['event_type'] ?? '') === 'api.charge.payment' && ($data['status'] ?? '') === 'success') {
                // PayChangu sends both 'reference' and 'tx_ref' – use tx_ref first
                $reference = $data['tx_ref'] ?? $data['reference'];
                Log::info('Processing successful payment', ['reference' => $reference]);

                $transaction = Transaction::where('reference', $reference)->first();
                if (!$transaction) {
                    Log::error('Transaction not found', ['reference' => $reference]);
                    return response()->json(['error' => 'Transaction not found'], 404);
                }

                if ($transaction->status === 'completed') {
                    return response()->json(['message' => 'Already processed'], 200);
                }

                DB::transaction(function () use ($transaction, $data) {
                    $transaction->update([
                        'status' => 'completed',
                        'payment_details' => json_encode($data['authorization'] ?? []),
                        'paid_at' => now(),
                    ]);
                    $this->updateRelatedModel($transaction);
                });

                Log::info('Webhook processed successfully', ['transaction_id' => $transaction->id]);
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('Webhook exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}