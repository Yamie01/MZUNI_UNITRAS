<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionUsage;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Paychangu\Laravel\Facades\Paychangu;

class SubscriptionController extends Controller
{
    public function index()
    {
        $activeSubscription = Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();
            
        $pastSubscriptions = Subscription::where('user_id', auth()->id())
            ->where(function($q) {
                $q->where('status', 'expired')->orWhere('end_date', '<', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        $plans = [
            'weekly' => [
                'name' => 'Weekly Pass',
                'price' => 5000,
                'duration' => '7 days',
                'rides_per_day' => 2,
                'savings' => 'Save 30% compared to pay-as-you-go'
            ],
            'monthly' => [
                'name' => 'Monthly Pass',
                'price' => 15000,
                'duration' => '30 days',
                'rides_per_day' => 4,
                'savings' => 'Save 50% compared to pay-as-you-go'
            ]
        ];
        
        $usageStats = null;
        if ($activeSubscription) {
            $usageStats = [
                'today_used' => $activeSubscription->getTodaysUsageCount(),
                'today_limit' => $activeSubscription->getDailyLimit(),
                'remaining_today' => $activeSubscription->getRemainingTodaysRides(),
                'total_used' => $activeSubscription->usages()->count(),
                'days_remaining' => $activeSubscription->getRemainingDays(),
            ];
        }
        
        return view('subscription.index', compact('activeSubscription', 'pastSubscriptions', 'plans', 'usageStats'));
    }
    
    public function subscribe(Request $request)
{
    $request->validate([
        'plan' => 'required|in:weekly,monthly'
    ]);
    
    $plan = $request->plan;
    $price = $plan === 'weekly' ? 5000 : 15000;
    $durationDays = $plan === 'weekly' ? 7 : 30;
    
    // Check if user already has active subscription
    $existingActive = Subscription::where('user_id', auth()->id())
        ->where('status', 'active')
        ->where('end_date', '>', now())
        ->first();
        
    if ($existingActive) {
        return back()->with('error', 'You already have an active subscription.');
    }
    
    $txRef = 'SUB-' . auth()->id() . '-' . time();
    
    // Create pending subscription first
    $subscription = Subscription::create([
        'user_id' => auth()->id(),
        'type' => $plan,
        'status' => 'pending',
        'price' => $price,
        'start_date' => now(),
        'end_date' => now()->addDays($durationDays),
    ]);
    
    $transaction = Transaction::create([
        'reference' => $txRef,
        'transaction_type' => 'subscription',
        'transaction_id' => $subscription->id,
        'amount' => $price,
        'platform_fee' => $price * 0.20,
        'owner_earnings' => 0,
        'status' => 'pending',
    ]);
    
    try {
        $response = Paychangu::create_checkout_link([
            'amount' => $price,  // ✅ NO MULTIPLICATION
            'email' => auth()->user()->email,
            'first_name' => auth()->user()->name,
            'last_name' => '',
            'currency' => 'MWK',
            'return_url' => url('/subscription/callback'),
            'callback_url' => url('/payment/webhook'),
            'meta' => [
                'transaction_id' => $transaction->id,
                'subscription_id' => $subscription->id,
                'user_id' => auth()->id(),
                'tx_ref' => $txRef,
                'plan' => $plan,
                'payment_type' => 'subscription',
            ],
        ]);
        
        if ($response['success']) {
            session([
                'pending_subscription_id' => $subscription->id,
                'pending_transaction_id' => $transaction->id,
            ]);
            return redirect($response['checkout_url']);
        }
        
        return back()->with('error', 'Unable to initiate subscription payment.');
    } catch (\Exception $e) {
        Log::error('Subscription payment error: ' . $e->getMessage());
        return back()->with('error', 'Payment service error.');
    }
    }
    public function callback(Request $request)
{
    $tx_ref = $request->query('tx_ref');
    $status = $request->query('status');
    
    if (!$tx_ref || $status !== 'success') {
        return redirect()->route('subscription.index')->with('error', 'Payment was not completed.');
    }
    
    $transaction = Transaction::where('reference', $tx_ref)->first();
    
    if (!$transaction) {
        return redirect()->route('subscription.index')->with('error', 'Transaction not found.');
    }
    
    $verification = Paychangu::verify_checkout($tx_ref);
    
    if ($verification['success'] && ($verification['data']['status'] ?? '') === 'success') {
        DB::transaction(function () use ($transaction, $verification) {
            $transaction->update([
                'status' => 'completed',
                'paid_at' => now(),
            ]);
            
            // Find subscription by transaction_id
            $subscription = Subscription::find($transaction->transaction_id);
            if ($subscription) {
                $subscription->update(['status' => 'active']);
            }
        });
        
        return redirect()->route('subscription.index')->with('success', 'Subscription activated!');
    }
    
    return redirect()->route('subscription.index')->with('error', 'Payment verification failed.');
    }
    public function cancel(Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            abort(403);
        }
        
        if (!$subscription->isActive()) {
            return back()->with('error', 'This subscription is already expired.');
        }
        
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        return back()->with('success', 'Subscription cancelled. You can use it until ' . $subscription->end_date->format('d M Y'));
    }
}