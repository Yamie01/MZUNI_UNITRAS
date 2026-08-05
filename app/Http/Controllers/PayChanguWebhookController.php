<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Payout;
use App\Models\PlatformRevenue;
use App\Models\User;
use App\Models\StaffPayoutDetail;
use App\Services\PayChanguService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PayChanguWebhookController extends Controller
{
    protected $payChanguService;

    public function __construct(PayChanguService $payChanguService)
    {
        $this->payChanguService = $payChanguService;
    }

    /**
     * 🎯 Handle PayChangu webhook - The Magic Happens Here!
     * This automatically processes payments and initiates direct payouts
     */
    public function handleWebhook(Request $request)
    {
        Log::info('📨 PayChangu Webhook Received', [
            'payload' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        // Verify webhook signature
        $signature = $request->header('X-PayChangu-Signature');
        if (!$this->payChanguService->verifyWebhookSignature($request->getContent(), $signature)) {
            Log::error('❌ Invalid webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            DB::beginTransaction();

            $payload = $request->all();
            $reference = $payload['reference'] ?? null;
            $status = $payload['status'] ?? null;

            if (!$reference || !$status) {
                Log::error('❌ Invalid webhook payload');
                DB::rollBack();
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            // Find transaction
            $transaction = Transaction::where('reference', $reference)->first();
            if (!$transaction) {
                Log::error('❌ Transaction not found', ['reference' => $reference]);
                DB::rollBack();
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            // Get booking
            $booking = Booking::find($transaction->booking_id);
            if (!$booking) {
                Log::error('❌ Booking not found', ['booking_id' => $transaction->booking_id]);
                DB::rollBack();
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // Get vehicle owner
            $vehicle = $booking->vehicle;
            $owner = User::find($vehicle->owner_id);
            if (!$owner) {
                Log::error('❌ Vehicle owner not found', ['owner_id' => $vehicle->owner_id]);
                DB::rollBack();
                return response()->json(['error' => 'Owner not found'], 404);
            }

            // Process based on payment status
            if ($status === 'completed' || $status === 'success') {
                $this->processSuccessfulPayment($transaction, $booking, $owner, $payload);
            } else {
                $this->processFailedPayment($transaction, $booking, $payload);
            }

            DB::commit();

            Log::info('✅ Webhook processed successfully', [
                'reference' => $reference,
                'status' => $status,
                'booking_id' => $booking->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully',
                'reference' => $reference
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Webhook processing failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🔥 Process successful payment - 80/20 Split & Direct Payout
     */
    private function processSuccessfulPayment($transaction, $booking, $owner, $payload)
    {
        Log::info('💰 Processing successful payment with 80/20 split', [
            'booking_id' => $booking->id,
            'owner_id' => $owner->id,
            'amount' => $transaction->amount
        ]);

        // 1. Update transaction
        $transaction->update([
            'status' => 'completed',
            'payment_data' => $payload,
            'paid_at' => now()
        ]);

        // 2. Update booking
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_confirmed_at' => now()
        ]);

        // 3. Calculate 80/20 split
        $totalAmount = (float) $transaction->amount;
        $ownerShare = $totalAmount * 0.80;  // 80% to vehicle owner
        $platformShare = $totalAmount * 0.20; // 20% to platform

        Log::info('📊 Revenue split calculated', [
            'total' => $totalAmount,
            'owner_share' => $ownerShare,
            'platform_share' => $platformShare
        ]);

        // 4. Get owner's payout details
        $staffDetails = StaffPayoutDetail::where('user_id', $owner->id)->first();

        // 5. Create payout record
        $payout = Payout::create([
            'booking_id' => $booking->id,
            'owner_id' => $owner->id,
            'transaction_id' => $transaction->id,
            'total_amount' => $totalAmount,
            'owner_share' => $ownerShare,
            'platform_share' => $platformShare,
            'amount' => $ownerShare,
            'staff_id' => $owner->staff_id ?? 'STAFF-' . $owner->id,
            'staff_name' => $owner->name,
            'department' => $owner->department ?? 'Unknown',
            'payout_method' => $staffDetails->preferred_payout_method ?? 'mobile_money',
            'mobile_money_provider' => $staffDetails->mobile_money_provider ?? null,
            'mobile_money_number' => $staffDetails->mobile_money_number ?? null,
            'bank_name' => $staffDetails->bank_name ?? null,
            'bank_account_number' => $staffDetails->bank_account_number ?? null,
            'bank_account_name' => $staffDetails->bank_account_name ?? $owner->name,
            'reference' => 'PAYOUT-' . $booking->id . '-' . time(),
            'status' => $staffDetails && $staffDetails->hasPayoutMethod() ? 'pending' : 'pending_details',
            'metadata' => [
                'booking_reference' => $booking->booking_reference ?? 'BK-' . $booking->id,
                'seats_booked' => $booking->seats_booked,
                'payment_reference' => $transaction->reference,
                'split_percentage' => '80/20'
            ]
        ]);

        Log::info('✅ Payout record created', ['payout_id' => $payout->id]);

        // 6. 🎯 INITIATE DIRECT PAYOUT TO OWNER
        if ($staffDetails && $staffDetails->hasPayoutMethod()) {
            $this->initiateDirectPayout($owner, $payout, $staffDetails);
        } else {
            Log::warning('⚠️ Owner has no payout details configured', [
                'owner_id' => $owner->id,
                'payout_id' => $payout->id
            ]);
            // Mark as pending details - owner needs to update their payout info
            $payout->markAsPendingDetails();
        }

        // 7. Update owner's balance
        $owner->increment('available_balance', $ownerShare);
        $owner->increment('lifetime_earnings', $ownerShare);

        // 8. Record platform revenue (20%)
        $this->recordPlatformRevenue($platformShare, $booking, $payout);

        Log::info('✅ Payment processing complete', [
            'booking_id' => $booking->id,
            'owner_share' => $ownerShare,
            'platform_share' => $platformShare,
            'payout_id' => $payout->id
        ]);
    }

    /**
     * 🎯 Initiate Direct Payout to Owner
     */
    private function initiateDirectPayout($owner, $payout, $staffDetails)
    {
        try {
            Log::info('💸 Initiating direct payout to vehicle owner', [
                'owner_id' => $owner->id,
                'amount' => $payout->owner_share,
                'method' => $staffDetails->preferred_payout_method
            ]);

            // Prepare payout data
            $payoutData = [
                'amount' => $payout->owner_share,
                'reference' => $payout->reference,
                'booking_id' => $payout->booking_id,
                'owner_id' => $owner->id,
                'narration' => 'MZUNI UNITRAS - Ride Payout (80%)',
                'method' => $staffDetails->preferred_payout_method,
                'account_name' => $owner->name,
            ];

            // Add method-specific details
            if ($staffDetails->preferred_payout_method === 'mobile_money') {
                $payoutData['mobile_provider'] = strtolower($staffDetails->mobile_money_provider);
                $payoutData['mobile_number'] = $staffDetails->mobile_money_number;
            } else {
                $payoutData['bank_name'] = $staffDetails->bank_name;
                $payoutData['account_number'] = $staffDetails->bank_account_number;
                $payoutData['account_name'] = $staffDetails->bank_account_name ?? $owner->name;
            }

            // 🔥 Call PayChangu Payout API
            $payoutResponse = $this->payChanguService->initiatePayout($payoutData);

            // Update payout with response
            $payout->markAsProcessing($payoutResponse);

            Log::info('✅ Direct payout initiated successfully', [
                'payout_id' => $payout->id,
                'payout_reference' => $payout->reference,
                'response' => $payoutResponse
            ]);

            // Dispatch job to verify payout status
            dispatch(new \App\Jobs\VerifyPayoutStatus($payout->id))
                ->delay(now()->addMinutes(5));

        } catch (\Exception $e) {
            Log::error('❌ Direct payout failed', [
                'owner_id' => $owner->id,
                'payout_id' => $payout->id,
                'error' => $e->getMessage()
            ]);

            // Mark payout as failed
            $payout->markAsFailed($e->getMessage());

            // Move amount to pending balance
            $owner->decrement('available_balance', $payout->owner_share);
            $owner->increment('pending_balance', $payout->owner_share);

            // Notify admin
            $this->notifyAdminOfFailedPayout($owner, $payout, $e);
        }
    }

    /**
     * Record platform revenue (20%)
     */
    private function recordPlatformRevenue($platformShare, $booking, $payout)
    {
        $platformRevenue = PlatformRevenue::firstOrCreate(
            ['date' => now()->toDateString()],
            [
                'total_revenue' => 0,
                'rides_revenue' => 0,
                'rentals_revenue' => 0,
                'subscriptions_revenue' => 0,
                'breakdown' => []
            ]
        );

        $platformRevenue->increment('total_revenue', $platformShare);
        $platformRevenue->increment('rides_revenue', $platformShare);

        $breakdown = $platformRevenue->breakdown ?? [];
        $breakdown[] = [
            'booking_id' => $booking->id,
            'payout_id' => $payout->id,
            'amount' => $platformShare,
            'type' => 'ride_share',
            'timestamp' => now()->toDateTimeString()
        ];
        $platformRevenue->update(['breakdown' => $breakdown]);

        Log::info('📊 Platform revenue recorded', [
            'date' => now()->toDateString(),
            'amount' => $platformShare,
            'total' => $platformRevenue->total_revenue
        ]);
    }

    private function processFailedPayment($transaction, $booking, $payload)
    {
        Log::warning('⚠️ Payment failed', [
            'transaction_id' => $transaction->id,
            'booking_id' => $booking->id
        ]);

        $transaction->update([
            'status' => 'failed',
            'payment_data' => $payload
        ]);

        $booking->update([
            'payment_status' => 'failed',
            'status' => 'payment_failed'
        ]);

        // Restore vehicle seats
        $vehicle = $booking->vehicle;
        $vehicle->increment('available_seats', $booking->seats_booked);
    }

    private function notifyAdminOfFailedPayout($owner, $payout, $exception)
    {
        // You can implement email/SMS notification here
        Log::alert('🚨 ADMIN ALERT: Direct payout failed', [
            'owner_id' => $owner->id,
            'owner_name' => $owner->name,
            'payout_id' => $payout->id,
            'amount' => $payout->owner_share,
            'error' => $exception->getMessage()
        ]);
    }
}