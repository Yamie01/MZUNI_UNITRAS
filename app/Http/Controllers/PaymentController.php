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
        $platformFee = $totalAmount * 0.20;
        $ownerEarnings = $totalAmount * 0.80;
        $txRef = 'TXN-BOOK-' . $booking->id . '-' . time();

        $transaction = Transaction::create([
            'reference'        => $txRef,
            'transaction_type' => 'booking',
            'transaction_id'   => $booking->id,
            'amount'           => $totalAmount,
            'platform_fee'     => $platformFee,
            'owner_earnings'   => $ownerEarnings,
            'status'           => 'pending',
        ]);

        $booking->update([
            'platform_fee'   => $platformFee,
            'owner_earnings' => $ownerEarnings,
        ]);

        try {
            $response = Paychangu::create_checkout_link([
                'amount'       => $totalAmount,
                'email'        => auth()->user()->email,
                'first_name'   => auth()->user()->name,
                'last_name'    => '',
                'currency'     => 'MWK',
                'return_url'   => url('/payment/return'),
                'callback_url' => url('/payment/webhook'),
                'meta' => [
                    'transaction_id' => $transaction->id,
                    'booking_id'     => $booking->id,
                    'user_id'        => auth()->id(),
                    'tx_ref'         => $txRef,
                    'payment_type'   => 'ride_booking',
                ],
            ]);

            if ($response['success']) {
                // Update with the actual reference from PayChangu
                $actualTxRef = $response['tx_ref'];
                $transaction->update(['reference' => $actualTxRef]);
                
                session([
                    'pending_transaction_id' => $transaction->id,
                    'pending_booking_id'     => $booking->id,
                ]);
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

    // Update or create transaction
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
            'return_url' => url('/payment/return'),
            'callback_url' => url('/payment/webhook'),
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
     * Return URL – user comes back after payment
     */
    public function handleReturn(Request $request)
    {
        $tx_ref = $request->query('tx_ref');
        $status = $request->query('status');

        if (!$tx_ref || $status !== 'success') {
            return redirect()->route('dashboard')->with('error', 'Payment was not completed.');
        }

        $transaction = Transaction::where('reference', $tx_ref)->first();
        
        if (!$transaction) {
            return redirect()->route('dashboard')->with('error', 'Transaction not found.');
        }

        // Verify with PayChangu
        $verification = Paychangu::verify_checkout($tx_ref);
        
        if (!$verification['success'] || ($verification['data']['status'] ?? '') !== 'success') {
            return redirect()->route('dashboard')->with('error', 'Payment verification failed.');
        }

        if ($transaction->status === 'completed') {
            return $this->redirectToSuccess($transaction);
        }

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

    /**
     * Manual verification fallback
     */
    public function manualVerify(Request $request)
{
    $rentalId = $request->input('rental_id');
    $rental = BikeRental::find($rentalId);
    
    if (!$rental) {
        return back()->with('error', 'Rental not found.');
    }
    
    // Find the transaction
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
        if ($transaction->transaction_type === 'booking') {
            $booking = Booking::find($transaction->transaction_id);
            if ($booking && !$booking->is_paid) {
                $booking->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                    'payment_date' => now(),
                ]);
            }
        } elseif ($transaction->transaction_type === 'bike_rental') {
            $rental = BikeRental::find($transaction->transaction_id);
            if ($rental && !$rental->is_paid) {
                $rental->update([
                    'is_paid' => true,
                    'status' => 'active',
                    'payment_date' => now(),
                ]);
                if ($rental->bike) {
                    $rental->bike->update(['status' => 'rented']);
                }
            }
        }
    }

    public function handleCancel(Request $request)
    {
        return redirect()->route('dashboard')->with('error', 'You cancelled the payment.');
    }

    public function handleWebhook(Request $request)
    {
    $payload = $request->getContent();
    $signature = $request->header('X-Signature');
    $webhookSecret = config('paychangu.webhook_secret');
    $computedSignature = hash_hmac('sha256', $payload, $webhookSecret);

    if ($computedSignature !== $signature) {
        Log::warning('Invalid webhook signature');
        return response()->json(['error' => 'Invalid signature'], 401);
    }

    $data = $request->all();
    
    if (($data['event_type'] ?? '') === 'api.charge.payment' && ($data['status'] ?? '') === 'success') {
        $transaction = Transaction::where('reference', $data['reference'])->first();
        
        if ($transaction && $transaction->status !== 'completed') {
            DB::transaction(function () use ($transaction, $data) {
                $transaction->update([
                    'status' => 'completed',
                    'payment_details' => json_encode($data['authorization'] ?? []),
                    'paid_at' => now(),
                ]);
                
                // ✅ Handle subscription payments
                if ($transaction->transaction_type === 'subscription') {
                    $subscription = Subscription::find($transaction->transaction_id);
                    if ($subscription && $subscription->status !== 'active') {
                        $subscription->update(['status' => 'active']);
                        Log::info('Subscription activated via webhook', [
                            'subscription_id' => $subscription->id,
                            'user_id' => $subscription->user_id
                        ]);
                    }
                } else {
                    // Handle booking or bike rental
                    $this->updateRelatedModel($transaction);
                }
            });
        }
    }

    return response()->json(['status' => 'ok'], 200);
    }
}