<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use App\Models\Booking;
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
        $pendingVehicles = $vehicles->where('is_approved', false)->count();

        // ---- ADVERTISEMENTS ----
        $totalAds = VehicleAdvertisement::where('owner_id', $ownerId)->count();
        $activeAds = VehicleAdvertisement::where('owner_id', $ownerId)
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->get();

        // ---- BOOKINGS & EARNINGS ----
        $bookingsQuery = Booking::whereHas('advertisement', function ($query) use ($ownerId) {
            $query->where('owner_id', $ownerId);
        });

        $totalBookings = $bookingsQuery->count();
        $pendingBookings = $bookingsQuery->clone()->where('status', 'pending')->count();
        $completedBookings = $bookingsQuery->clone()->where('status', 'completed');

        $earnings = $completedBookings->sum('total_price');
        $completedTrips = $completedBookings->count();

        // ---- RECENT BOOKINGS (last 5) ----
        $recentBookings = $bookingsQuery->clone()
            ->with(['user', 'advertisement'])
            ->latest()
            ->take(5)
            ->get();

        // ---- MONTHLY EARNINGS CHART (current year) ----
        $monthlyEarnings = $bookingsQuery->clone()
            ->where('status', 'completed')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total_price) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            if (!isset($monthlyEarnings[$i])) {
                $monthlyEarnings[$i] = 0;
            }
        }
        ksort($monthlyEarnings);

        // ---- STATS ARRAY (for easy access in view) ----
        $stats = [
            'vehicles' => $vehicles->count(),
            'pending_vehicles' => $pendingVehicles,
            'total_ads' => $totalAds,
            'active_ads_count' => $activeAds->count(),
            'total_bookings' => $totalBookings,
            'pending_bookings' => $pendingBookings,
            'earnings' => $earnings,
            'completed_trips' => $completedTrips,
            'earnings_growth' => 0, // Could be calculated from previous month if needed
        ];

        // Extract commonly used variables for the view (to avoid undefined variable errors)
        $vehiclesCount = $stats['vehicles'];
        $pendingVehiclesCount = $stats['pending_vehicles'];
        $totalAdsCount = $stats['total_ads'];
        $activeAdsCount = $stats['active_ads_count'];
        $totalBookingsCount = $stats['total_bookings'];
        $pendingBookingsCount = $stats['pending_bookings'];
        $earningsAmount = $stats['earnings'];
        $completedTripsCount = $stats['completed_trips'];

        return view('vehicle-owner.dashboard', compact(
            'user',
            'vehicles',
            'activeAds',
            'recentBookings',
            'monthlyEarnings',
            'stats',
            // Explicitly include all scalar variables that the view might use
            'vehiclesCount',
            'pendingVehiclesCount',
            'totalAdsCount',
            'activeAdsCount',
            'totalBookingsCount',
            'pendingBookingsCount',
            'earningsAmount',
            'completedTripsCount',
            // Also keep original names for backward compatibility
            'totalBookings',
            'pendingBookings',
            'earnings',
            'completedTrips'
        ));
    }
}