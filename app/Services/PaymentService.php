<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Booking;
use App\Models\BikeRental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function finalizeTransaction(Transaction $transaction, array $paymentData)
    {
        if ($transaction->status === 'completed') {
            return; // Already processed
        }

        DB::transaction(function () use ($transaction, $paymentData) {
            // Update Transaction
            $transaction->update([
                'status'          => 'completed',
                'payment_details' => json_encode($paymentData['authorization'] ?? []),
                'paid_at'         => now(),
            ]);

            // Update Related Model
            if ($transaction->transaction_type === 'booking') {
                $booking = Booking::find($transaction->transaction_id);
                if ($booking && !$booking->is_paid) {
                    $booking->update([
                        'is_paid'      => true,
                        'status'       => 'confirmed',
                        'payment_date' => now(),
                    ]);

                    // Optional: Reduce advertisement seats (already done at booking creation)
                }
            } elseif ($transaction->transaction_type === 'bike_rental') {
                $rental = BikeRental::find($transaction->transaction_id);
                if ($rental && !$rental->is_paid) {
                    $rental->update([
                        'is_paid'      => true,
                        'status'       => 'active',
                        'payment_date' => now(),
                    ]);
                    // Mark the bike as rented
                    if ($rental->bike) {
                        $rental->bike->update(['status' => 'rented']);
                    }
                }
            }

            Log::info('Payment finalized via Webhook/Service', [
                'reference' => $transaction->reference,
                'type'      => $transaction->transaction_type,
                'amount'    => $transaction->amount,
            ]);
        });
    }
}