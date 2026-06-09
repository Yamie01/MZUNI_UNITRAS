<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema; // optional, we'll skip bike locations safely

class HomeController extends Controller
{
    /**
     * Show the welcome page with available vehicles, bikes, stats, and locations.
     */
    public function index()
    {
        // 1. Available rides
        $availableVehicles = VehicleAdvertisement::with(['vehicle', 'owner'])
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

        // 4. Collect unique locations for autocomplete (only from rides for now)
        //    This avoids the 'pickup_location' column error.
        $rideFromLocations = VehicleAdvertisement::where('status', 'approved')
            ->where('departure_time', '>', now())
            ->pluck('from_location')
            ->unique()
            ->values()
            ->toArray();

        $rideToLocations = VehicleAdvertisement::where('status', 'approved')
            ->where('departure_time', '>', now())
            ->pluck('to_location')
            ->unique()
            ->values()
            ->toArray();

        // Merge both ride locations
        $locations = array_unique(array_merge($rideFromLocations, $rideToLocations));
        sort($locations);

        // Single return with all variables
        return view('welcome', compact('availableVehicles', 'availableBikes', 'stats', 'locations'));
    }

    /**
     * Show the dashboard based on user role.
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
     * Search for rides.
     */
    public function search(Request $request)
    {
        $query = VehicleAdvertisement::with(['vehicle', 'owner'])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0);

        if ($request->filled('from')) {
            $query->where('from_location', 'like', '%' . $request->from . '%');
        }

        if ($request->filled('to')) {
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