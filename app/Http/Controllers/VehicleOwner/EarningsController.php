<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get all completed transactions for this owner's ads using direct join
        $transactions = Transaction::join('bookings', 'transactions.transaction_id', '=', 'bookings.id')
            ->join('vehicle_advertisements', 'bookings.vehicle_advertisement_id', '=', 'vehicle_advertisements.id')
            ->where('transactions.transaction_type', 'booking')
            ->where('transactions.status', 'completed')
            ->where('vehicle_advertisements.owner_id', $user->id)
            ->select('transactions.*')
            ->orderBy('transactions.paid_at', 'desc')
            ->get();
        
        $totalEarnings = $transactions->sum('owner_earnings');
        $completedTrips = $transactions->count();
        $platformCommission = $transactions->sum('platform_fee');
        $pendingPayout = $totalEarnings; // later subtract withdrawn amount
        
        // Recent completed bookings (for reference)
        $recentBookings = Booking::whereHas('advertisement', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->where('status', 'completed')
            ->with(['user', 'advertisement'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('vehicle-owner.earnings', compact(
            'transactions', 'totalEarnings', 'completedTrips', 'platformCommission',
            'pendingPayout', 'recentBookings'
        ));
    }
    
    public function withdraw(Request $request)
    {
        // Placeholder – later integrate actual withdrawal logic
        return back()->with('info', 'Withdrawal feature is coming soon. Please contact admin for manual payout.');
    }
}