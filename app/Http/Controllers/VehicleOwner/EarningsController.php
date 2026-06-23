<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EarningsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalEarnings = Booking::whereHas('advertisement', function($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->where('status', 'completed')->sum('owner_earnings');

        $payouts = Payout::where('user_id', $user->id)->latest()->get();

        $totalPayouts = $payouts->where('status', 'completed')->sum('amount');
        $pendingPayouts = $payouts->where('status', 'pending')->sum('amount');

        return view('vehicle-owner.earnings.index', compact(
            'totalEarnings',
            'payouts',
            'totalPayouts',
            'pendingPayouts'
        ));
    }
}