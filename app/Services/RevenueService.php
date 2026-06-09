<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RevenueService
{
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
        ];
    }
}