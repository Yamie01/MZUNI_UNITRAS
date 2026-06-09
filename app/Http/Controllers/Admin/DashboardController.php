<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Bike;
use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_vehicles' => Vehicle::where('is_approved', true)->count(),
            'pending_vehicles' => Vehicle::where('is_approved', false)->count(),
            'total_bikes' => Bike::count(),
            'available_bikes' => Bike::where('status', 'available')->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'total_rentals' => BikeRental::count(),
            'active_rentals' => BikeRental::where('status', 'active')->count(),
            'completed_rentals' => BikeRental::where('status', 'completed')->count(),
            // ✅ CORRECT: Sum of original amounts (not multiplied)
            'rental_revenue' => BikeRental::where('status', 'completed')->sum('total_amount'),
            'booking_revenue' => Booking::where('status', 'completed')->sum('total_price'),
            'total_revenue' => Booking::where('status', 'completed')->sum('total_price') 
                             + BikeRental::where('status', 'completed')->sum('total_amount'),
        ];
        
        // Recent users
        $recentUsers = User::latest()->take(5)->get();
        
        // Recent bike rentals
        $recentRentals = BikeRental::with(['bike', 'user'])
            ->latest()
            ->take(5)
            ->get();
        
        // Recent bookings
        $recentBookings = Booking::with(['user', 'advertisement'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentRentals', 'recentBookings'));
    }
}