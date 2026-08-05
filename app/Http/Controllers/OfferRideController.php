<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
=======
use App\Models\Location;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)

class OfferRideController extends Controller
{
    /**
<<<<<<< HEAD
     * Redirect to the offer ride page.
     * If user is not a vehicle owner, redirect to become one.
     * If user doesn't have an approved vehicle, redirect to add one.
=======
     * Show the offer ride page - redirects vehicle owners directly to publish page
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function index()
    {
        $user = Auth::user();
<<<<<<< HEAD
        
        // If user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to offer a ride.');
        }
        
        // If user is not a vehicle owner, redirect to become one
        if ($user->user_type !== 'vehicle_owner') {
            return redirect()->route('become.vehicle.owner')
                ->with('info', 'You need to register as a Vehicle Owner first.');
        }

        // Check if user has at least one approved vehicle
        $hasApprovedVehicle = $user->vehicles()->where('is_approved', true)->exists();

        if (!$hasApprovedVehicle) {
            $hasPendingVehicle = $user->vehicles()->where('is_approved', false)->exists();
            
            if ($hasPendingVehicle) {
                return redirect()->route('vehicle-owner.vehicles.index')
                    ->with('warning', 'You have a vehicle pending approval. Please wait for admin verification.');
            }
            
            return redirect()->route('vehicle-owner.vehicles.create')
                ->with('info', 'Please add your vehicle and wait for admin approval.');
        }

        // All good – redirect to publish ride
        return redirect()->route('vehicle-owner.advertisements.create');
=======

        // If user is already a vehicle owner, redirect to publish ride page
        if ($user->isVehicleOwner()) {
            return redirect()->route('vehicle-owner.advertisements.create')
                ->with('info', 'You are already a vehicle owner. Publish your ride here.');
        }

        // Check if user has vehicles
        $hasVehicles = Vehicle::where('owner_id', $user->id)->exists();

        // Get locations for the form
        $locations = Location::orderBy('name')->get();

        return view('offer-ride', compact('hasVehicles', 'locations'));
    }

    /**
     * Process the offer ride request
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // If user is already a vehicle owner, redirect to publish page
        if ($user->isVehicleOwner()) {
            return redirect()->route('vehicle-owner.advertisements.create')
                ->with('info', 'You are already a vehicle owner. Publish your ride here.');
        }

        // Validate request
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'from_location_id' => 'required|exists:locations,id',
            'to_location_id' => 'required|exists:locations,id',
            'departure_time' => 'required|date|after:now',
            'price' => 'required|numeric|min:100',
            'available_seats' => 'required|integer|min:1|max:10',
            'ad_type' => 'required|in:ride_share,taxi,bus',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            // Create advertisement
            $advertisement = VehicleAdvertisement::create([
                'vehicle_id' => $request->vehicle_id,
                'owner_id' => $user->id,
                'from_location_id' => $request->from_location_id,
                'to_location_id' => $request->to_location_id,
                'departure_time' => $request->departure_time,
                'price' => $request->price,
                'available_seats' => $request->available_seats,
                'ad_type' => $request->ad_type,
                'description' => $request->description,
                'status' => 'pending', // Needs admin approval
                'is_active' => true,
            ]);

            // If user has no vehicles, create one
            if (!Vehicle::where('owner_id', $user->id)->exists()) {
                // Redirect to add vehicle page
                return redirect()->route('vehicle-owner.vehicles.create')
                    ->with('info', 'Please add a vehicle first before publishing a ride.');
            }

            // Make user a vehicle owner if not already
            if (!$user->isVehicleOwner()) {
                $user->update(['user_type' => 'vehicle_owner']);
            }

            return redirect()->route('vehicle-owner.advertisements.index')
                ->with('success', 'Ride published successfully! Waiting for admin approval.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to publish ride: ' . $e->getMessage());
        }
    }

    /**
     * Redirect vehicle owners directly to publish ride
     */
    public function publishRide()
    {
        $user = Auth::user();

        if (!$user->isVehicleOwner()) {
            return redirect()->route('offer.ride')
                ->with('error', 'You need to become a vehicle owner first.');
        }

        return redirect()->route('vehicle-owner.advertisements.create')
            ->with('info', 'Publish your ride here.');
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    }
}