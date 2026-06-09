<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\VehicleAdvertisement;
use App\Models\Bike;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get available rides (for display on dashboard)
        $availableRides = VehicleAdvertisement::with(['vehicle', 'owner'])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->limit(3)
            ->get();
        
        // Get available bikes
        $availableBikes = Bike::where('status', 'available')
            ->where('is_active', true)
            ->limit(4)
            ->get();
        
        // Get recent ride bookings
        $recentBookings = Booking::with(['advertisement'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Get recent bike rentals
        $recentBikeRentals = BikeRental::with(['bike'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate totals
        $totalTrips = Booking::where('user_id', $user->id)->count();
        $totalSpent = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');
        
        return view('user.dashboard', compact(
            'availableRides',
            'availableBikes',
            'recentBookings',
            'recentBikeRentals',
            'totalTrips',
            'totalSpent'
        ));
    }
}