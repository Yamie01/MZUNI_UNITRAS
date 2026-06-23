<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show vehicle owner dashboard with statistics.
     */
    public function index()
    {
        $user = Auth::user();
        $ownerId = $user->id;

        // ---- VEHICLES ----
        $vehicles = $user->vehicles()->latest()->get();
        $pendingVehiclesCount = $vehicles->where('is_approved', false)->count();
        $activeVehicles = Vehicle::where('owner_id', $ownerId)
            ->where('is_approved', true)
            ->count();

        // ---- ADVERTISEMENTS ----
        $totalAds = VehicleAdvertisement::where('owner_id', $ownerId)->count();
        $activeAds = VehicleAdvertisement::where('owner_id', $ownerId)
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->get();
        $activeAdsCount = $activeAds->count();

        // ---- RECENT ADS ----
        $recentAds = VehicleAdvertisement::where('owner_id', $ownerId)
            ->latest()
            ->take(3)
            ->get();

        // ---- RECENT VEHICLES ----
        $recentVehicles = Vehicle::where('owner_id', $ownerId)
            ->latest()
            ->take(3)
            ->get();

        // ---- BOOKINGS ----
        $bookingsQuery = Booking::whereHas('advertisement', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        });

        $totalBookings = $bookingsQuery->count();
        $pendingBookings = $bookingsQuery->clone()->where('status', 'pending')->count();

        // ---- EARNINGS (owner_earnings from completed trips) ----
        $completedBookingsQuery = $bookingsQuery->clone()->where('trip_status', 'completed');
        $completedTrips = $completedBookingsQuery->count();
        $totalEarnings = $completedBookingsQuery->sum('owner_earnings');

        // ---- MONTHLY EARNINGS CHART (current year) ----
        $monthlyEarnings = $bookingsQuery->clone()
            ->where('trip_status', 'completed')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(owner_earnings) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyEarnings[$i])) {
                $monthlyEarnings[$i] = 0;
            }
        }
        ksort($monthlyEarnings);

        // ---- RECENT BOOKINGS (last 5) ----
        $recentBookings = $bookingsQuery->clone()
            ->with(['user', 'advertisement'])
            ->latest()
            ->take(5)
            ->get();

        // ---- STATS ARRAY (for easy access) ----
        $stats = [
            'vehicles'           => $vehicles->count(),
            'pending_vehicles'   => $pendingVehiclesCount,
            'total_ads'          => $totalAds,
            'active_ads_count'   => $activeAdsCount,
            'total_bookings'     => $totalBookings,
            'pending_bookings'   => $pendingBookings,
            'earnings'           => $totalEarnings,
            'completed_trips'    => $completedTrips,
            'earnings_growth'    => 0, // Can be calculated later
        ];

        return view('vehicle-owner.dashboard', compact(
            'user',
            'vehicles',
            'activeVehicles',
            'activeAds',
            'recentVehicles',
            'recentAds',
            'recentBookings',
            'monthlyEarnings',
            'stats',
            'totalEarnings',
            'completedTrips',
            'pendingBookings',
            'activeAdsCount',
            'totalBookings',
            'totalAds',
            'pendingVehiclesCount'
        ));
    }
}