<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard – shows active rentals with live timers,
     * users with unpaid late fees, and statistics.
     */
    public function index()
    {
        // ============================================================
        // 1. ACTIVE RENTALS WITH TIMERS
        // ============================================================
        $activeRentals = BikeRental::with(['bike', 'user'])
            ->whereIn('status', ['active', 'rented'])
            ->get();

        // ============================================================
        // 2. USERS WITH UNPAID LATE FEES
        // ============================================================
        $usersWithUnpaidLateFee = User::whereHas('bikeRentals', function ($query) {
            $query->where('late_fee', '>', 0)
                ->where('late_fee_paid', false);
        })->get();

        $totalActiveRentals = $activeRentals->count();
        $totalUnpaidLateFees = BikeRental::where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->sum('late_fee');

        // ============================================================
        // 3. OVERVIEW STATISTICS
        // ============================================================
        $stats = [
            'total_users' => User::count(),
            'total_vehicles' => Vehicle::where('is_approved', true)->count(),
            'pending_vehicles' => Vehicle::where('is_approved', false)->count(),
            'total_bikes' => Bike::count(),
            'available_bikes' => Bike::where('status', 'available')->count(),
            'total_ads' => VehicleAdvertisement::where('status', 'approved')->count(),
            'pending_ads' => VehicleAdvertisement::where('status', 'pending')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_rentals' => $totalActiveRentals,
            'completed_rentals' => BikeRental::where('status', 'completed')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'completed_trips' => Booking::where('status', 'completed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'rental_revenue' => Payment::whereNotNull('bike_rental_id')
                ->where('status', 'completed')
                ->sum('amount'),
            'booking_revenue' => Payment::whereNotNull('booking_id')
                ->where('status', 'completed')
                ->sum('amount'),
            'users_growth' => 0,
            'bookings_growth' => 0,
            'total_unpaid_late_fees' => $totalUnpaidLateFees,
        ];

        // ============================================================
        // 4. MONTHLY REVENUE
        // ============================================================
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $revenue = Payment::where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $monthlyRevenue[$month] = $revenue;
        }

        // ============================================================
        // 5. RECENT ACTIVITY
        // ============================================================
        $recentUsers = User::latest()->limit(5)->get();
        $recentBookings = Booking::with(['user', 'advertisement'])
            ->latest()
            ->limit(5)
            ->get();
        $recentRentals = BikeRental::with(['user', 'bike'])
            ->latest()
            ->limit(5)
            ->get();

        // ============================================================
        // 6. PENDING APPROVALS
        // ============================================================
        $pendingVehicles = Vehicle::where('is_approved', false)->limit(5)->get();
        $pendingAds = VehicleAdvertisement::where('status', 'pending')
            ->with('owner')
            ->limit(5)
            ->get();

        // ============================================================
        // 7. BOOKINGS BY STATUS
        // ============================================================
        $bookingStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ============================================================
        // 8. REVENUE BREAKDOWN
        // ============================================================
        $revenueBreakdown = [
            'ride_bookings' => Payment::whereHas('booking')
                ->where('status', 'completed')
                ->sum('amount'),
            'bike_rentals' => Payment::whereNotNull('bike_rental_id')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return view('admin.dashboard', compact(
            'activeRentals',
            'usersWithUnpaidLateFee',
            'totalActiveRentals',
            'totalUnpaidLateFees',
            'stats',
            'monthlyRevenue',
            'recentUsers',
            'recentBookings',
            'recentRentals',
            'pendingVehicles',
            'pendingAds',
            'bookingStatus',
            'revenueBreakdown'
        ));
    }
}