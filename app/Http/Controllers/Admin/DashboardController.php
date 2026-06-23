<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BikeRental;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard – shows active rentals with live timers,
     * users with unpaid late fees, and statistics.
     */
    public function dashboard()
    {
        // Active rentals with timers
        $activeRentals = BikeRental::with(['bike', 'user'])
            ->whereIn('status', ['active', 'rented'])
            ->get();

        // Users with unpaid late fees
        $usersWithUnpaidLateFee = User::whereHas('bikeRentals', function ($query) {
            $query->where('late_fee', '>', 0)
                ->where('late_fee_paid', false);
        })->get();

        // Statistics
        $totalActiveRentals = $activeRentals->count();
        $totalUnpaidLateFees = BikeRental::where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->sum('late_fee');

        return view('admin.dashboard', compact(
            'activeRentals',
            'usersWithUnpaidLateFee',
            'totalActiveRentals',
            'totalUnpaidLateFees'
        ));
    }
}