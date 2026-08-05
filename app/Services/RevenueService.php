<?php

namespace App\Services;

use App\Models\Booking;
<<<<<<< HEAD
use App\Models\User;
use App\Models\Withdrawal;
=======
use App\Models\PlatformRevenue;
use App\Models\Payout;
use App\Models\User;
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueService
{
<<<<<<< HEAD
    protected $commissionRate;
    protected $minimumWithdrawal;
    
    public function __construct()
    {
        $this->commissionRate = env('SYSTEM_COMMISSION_RATE', 15);
        $this->minimumWithdrawal = env('MINIMUM_WITHDRAWAL', 5000);
    }
    
    /**
     * Calculate revenue split for a booking
     */
    public function calculateRevenueSplit($amount)
    {
        $systemCommission = ($amount * $this->commissionRate) / 100;
        $ownerEarnings = $amount - $systemCommission;
        
        return [
            'total' => round($amount, 2),
            'system_commission' => round($systemCommission, 2),
            'owner_earnings' => round($ownerEarnings, 2),
            'commission_rate' => $this->commissionRate,
            'owner_percentage' => 100 - $this->commissionRate,
        ];
    }
    
    /**
     * Process booking revenue
     */
    public function processBookingRevenue(Booking $booking)
    {
        $split = $this->calculateRevenueSplit($booking->total_price);
        
        DB::transaction(function () use ($booking, $split) {
            // Update booking with commission info
            $booking->update([
                'system_commission' => $split['system_commission'],
                'owner_earnings' => $split['owner_earnings'],
            ]);
            
            // Update owner's earnings
            $owner = $booking->advertisement->owner;
            $owner->increment('total_earnings', $split['owner_earnings']);
            $owner->increment('available_balance', $split['owner_earnings']);
            
            Log::info('Revenue processed for booking', [
                'booking_id' => $booking->id,
                'amount' => $split['total'],
                'commission' => $split['system_commission'],
                'owner_earnings' => $split['owner_earnings'],
            ]);
        });
        
        return $split;
    }
    
    /**
     * Request withdrawal
     */
    public function requestWithdrawal(User $user, $amount, $paymentMethod, $accountDetails)
    {
        if ($user->available_balance < $amount) {
            throw new \Exception('Insufficient balance. Available: MWK ' . number_format($user->available_balance, 2));
        }
        
        if ($amount < $this->minimumWithdrawal) {
            throw new \Exception('Minimum withdrawal amount is MWK ' . number_format($this->minimumWithdrawal, 2));
        }
        
        DB::transaction(function () use ($user, $amount, $paymentMethod, $accountDetails) {
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'account_details' => $accountDetails,
                'status' => 'pending',
            ]);
            
            $user->decrement('available_balance', $amount);
            
            Log::info('Withdrawal requested', [
                'user_id' => $user->id,
                'amount' => $amount,
                'withdrawal_id' => $withdrawal->id,
            ]);
        });
        
        return true;
    }
    
    /**
     * Get earnings summary for a user
     */
    public function getEarningsSummary(User $user)
    {
        return [
            'total_earnings' => $user->total_earnings,
            'available_balance' => $user->available_balance,
            'withdrawn_amount' => $user->withdrawn_amount,
            'commission_rate' => $this->commissionRate,
            'minimum_withdrawal' => $this->minimumWithdrawal,
=======
    /**
     * Record revenue for a completed booking (80/20 split)
     */
    public function recordBookingRevenue(Booking $booking)
    {
        try {
            DB::beginTransaction();

            // Calculate revenue split
            $totalAmount = (float) $booking->total_price;
            $ownerShare = $totalAmount * 0.80;  // 80% to vehicle owner
            $platformShare = $totalAmount * 0.20; // 20% to platform

            // Get the vehicle owner
            $vehicle = $booking->vehicle;
            $owner = $vehicle ? User::find($vehicle->owner_id) : null;

            // 1. Create or update platform revenue
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
                'amount' => $platformShare,
                'type' => 'ride_booking',
                'timestamp' => now()->toDateTimeString()
            ];
            $platformRevenue->update(['breakdown' => $breakdown]);

            // 2. If owner exists, update owner's earnings
            if ($owner) {
                // Create payout record
                $payout = Payout::create([
                    'booking_id' => $booking->id,
                    'owner_id' => $owner->id,
                    'transaction_id' => $booking->payment_reference ?? null,
                    'total_amount' => $totalAmount,
                    'owner_share' => $ownerShare,
                    'platform_share' => $platformShare,
                    'amount' => $ownerShare,
                    'staff_id' => $owner->staff_id ?? null,
                    'staff_name' => $owner->name,
                    'department' => $owner->department ?? 'Unknown',
                    'payout_method' => $owner->payout_method ?? 'mobile_money',
                    'mobile_money_provider' => $owner->mobile_money_provider ?? null,
                    'mobile_money_number' => $owner->mobile_money_number ?? null,
                    'bank_name' => $owner->bank_name ?? null,
                    'bank_account_number' => $owner->bank_account_number ?? null,
                    'bank_account_name' => $owner->bank_account_name ?? $owner->name,
                    'reference' => 'PAYOUT-BK-' . $booking->id . '-' . time(),
                    'status' => 'pending',
                    'metadata' => [
                        'booking_reference' => $booking->booking_reference,
                        'seats' => $booking->number_of_seats,
                        'payment_reference' => $booking->payment_reference,
                        'split_percentage' => '80/20'
                    ]
                ]);

                // Update owner's balance
                $owner->increment('available_balance', $ownerShare);
                $owner->increment('lifetime_earnings', $ownerShare);

                // Update booking with payout reference
                $booking->update([
                    'payout_id' => $payout->id,
                ]);

                Log::info('✅ Revenue recorded for booking', [
                    'booking_id' => $booking->id,
                    'owner_id' => $owner->id,
                    'owner_share' => $ownerShare,
                    'platform_share' => $platformShare,
                    'payout_id' => $payout->id
                ]);
            } else {
                Log::warning('⚠️ Vehicle owner not found for revenue recording', [
                    'booking_id' => $booking->id,
                    'vehicle_id' => $booking->vehicle_id
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'owner_share' => $ownerShare,
                'platform_share' => $platformShare,
                'total_amount' => $totalAmount
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Failed to record booking revenue', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Record revenue for a bike rental
     */
    public function recordBikeRentalRevenue($rental)
    {
        try {
            // Calculate revenue split
            $totalAmount = (float) $rental->total_amount;
            $ownerShare = $totalAmount * 0.80;  // 80% to bike owner
            $platformShare = $totalAmount * 0.20; // 20% to platform

            // Update platform revenue
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
            $platformRevenue->increment('rentals_revenue', $platformShare);

            $breakdown = $platformRevenue->breakdown ?? [];
            $breakdown[] = [
                'rental_id' => $rental->id,
                'amount' => $platformShare,
                'type' => 'bike_rental',
                'timestamp' => now()->toDateTimeString()
            ];
            $platformRevenue->update(['breakdown' => $breakdown]);

            Log::info('✅ Bike rental revenue recorded', [
                'rental_id' => $rental->id,
                'platform_share' => $platformShare
            ]);

            return [
                'success' => true,
                'platform_share' => $platformShare
            ];

        } catch (\Exception $e) {
            Log::error('❌ Failed to record bike rental revenue', [
                'rental_id' => $rental->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Record revenue for a subscription
     */
    public function recordSubscriptionRevenue($subscription, $amount)
    {
        try {
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

            $platformRevenue->increment('total_revenue', $amount);
            $platformRevenue->increment('subscriptions_revenue', $amount);

            $breakdown = $platformRevenue->breakdown ?? [];
            $breakdown[] = [
                'subscription_id' => $subscription->id,
                'amount' => $amount,
                'type' => 'subscription',
                'timestamp' => now()->toDateTimeString()
            ];
            $platformRevenue->update(['breakdown' => $breakdown]);

            Log::info('✅ Subscription revenue recorded', [
                'subscription_id' => $subscription->id,
                'amount' => $amount
            ]);

            return [
                'success' => true
            ];

        } catch (\Exception $e) {
            Log::error('❌ Failed to record subscription revenue', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get platform revenue summary
     */
    public function getPlatformRevenueSummary($startDate = null, $endDate = null)
    {
        $query = PlatformRevenue::query();

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        $revenues = $query->orderBy('date', 'desc')->get();

        return [
            'total_revenue' => $revenues->sum('total_revenue'),
            'rides_revenue' => $revenues->sum('rides_revenue'),
            'rentals_revenue' => $revenues->sum('rentals_revenue'),
            'subscriptions_revenue' => $revenues->sum('subscriptions_revenue'),
            'daily_breakdown' => $revenues
        ];
    }

    /**
     * Get owner earnings summary
     */
    public function getOwnerEarningsSummary($ownerId)
    {
        $owner = User::find($ownerId);

        if (!$owner) {
            return [
                'success' => false,
                'error' => 'Owner not found'
            ];
        }

        return [
            'success' => true,
            'available_balance' => $owner->available_balance ?? 0,
            'pending_balance' => $owner->pending_balance ?? 0,
            'lifetime_earnings' => $owner->lifetime_earnings ?? 0,
            'total_withdrawn' => $owner->total_withdrawn ?? 0,
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        ];
    }
}