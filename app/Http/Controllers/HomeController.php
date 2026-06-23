<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use App\Models\Location;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the welcome page with available rides, bikes, stats, and locations.
     */
    public function index()
    {
        // 1. Available rides (with location relationships)
        $availableVehicles = VehicleAdvertisement::with([
            'vehicle',
            'owner',
            'fromLocation',
            'toLocation'
        ])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->limit(6)
            ->get();

        // 2. Available bikes
        $availableBikes = Bike::where('status', 'available')
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // 3. Statistics
        $stats = [
            'total_vehicles' => Vehicle::where('is_approved', true)->count(),
            'total_users'    => User::count(),
            'completed_trips'=> Booking::where('status', 'completed')->count(),
        ];

        // 4. Locations for dropdowns (from the database)
        $locations = Location::orderBy('name')->get();

        return view('welcome', compact('availableVehicles', 'availableBikes', 'stats', 'locations'));
    }

    /**
     * Redirect to the appropriate dashboard based on user role.
     */
    public function dashboard()
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->user_type === 'vehicle_owner') {
            return redirect()->route('vehicle-owner.dashboard');
        }

        return view('user.dashboard');
    }

    /**
     * Search for rides (supports location IDs and text fallback).
     */
    public function search(Request $request)
    {
        $query = VehicleAdvertisement::with(['vehicle', 'owner', 'fromLocation', 'toLocation'])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0);

        // Search by location IDs (if provided)
        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        } elseif ($request->filled('from')) {
            // Fallback to text search
            $query->where('from_location', 'like', '%' . $request->from . '%');
        }

        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        } elseif ($request->filled('to')) {
            $query->where('to_location', 'like', '%' . $request->to . '%');
        }

        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        if ($request->filled('type')) {
            $query->where('ad_type', $request->type);
        }

        $advertisements = $query->paginate(12);

        return view('search', compact('advertisements'));
    }

    /**
     * Show about page.
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * Show contact page.
     */
    public function contact()
    {
        return view('pages.contact');
    }
}