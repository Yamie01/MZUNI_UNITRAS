<?php

namespace App\Http\Controllers;

use App\Events\VehicleLocationUpdated;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\VehicleLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    /**
     * Update vehicle location (driver/owner)
     */
    public function updateVehicleLocation(Request $request, Vehicle $vehicle)
    {
        if (Auth::id() !== $vehicle->owner_id && Auth::user()->user_type !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|min:0|max:360',
        ]);
        
        $location = VehicleLocation::create([
            'trackable_type' => 'vehicle',
            'trackable_id' => $vehicle->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'recorded_at' => now(),
        ]);
        
        broadcast(new VehicleLocationUpdated('vehicle', $vehicle->id, $request->latitude, $request->longitude, $request->speed, $request->heading));
        
        return response()->json(['success' => true, 'location' => $location]);
    }
    
    /**
     * Get vehicle latest location
     */
    public function getVehicleLocation(Vehicle $vehicle)
    {
        $location = $vehicle->latestLocation;
        return response()->json(['location' => $location]);
    }
    
    /**
     * Update bike location (renter)
     */
    public function updateBikeLocation(Request $request, Bike $bike)
    {
        $activeRental = BikeRental::where('bike_id', $bike->id)
            ->where('status', 'active')
            ->first();
        
        if (Auth::user()->user_type !== 'admin' && (!$activeRental || $activeRental->user_id !== Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        
        $location = VehicleLocation::create([
            'trackable_type' => 'bike',
            'trackable_id' => $bike->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recorded_at' => now(),
        ]);
        
        broadcast(new VehicleLocationUpdated('bike', $bike->id, $request->latitude, $request->longitude));
        
        return response()->json(['success' => true, 'location' => $location]);
    }
    
    /**
     * Get bike latest location
     */
    public function getBikeLocation(Bike $bike)
    {
        $location = $bike->latestLocation;
        return response()->json(['location' => $location]);
    }
    
    /**
     * Show tracking page for passenger
     */
    public function showTracking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && 
            $booking->advertisement->owner_id !== Auth::id() && 
            Auth::user()->user_type !== 'admin') {
            abort(403);
        }
        
        $vehicle = $booking->vehicle;
        return view('tracking.ride', compact('booking', 'vehicle'));
    }
    
    /**
     * Show bike tracking page for renter
     */
    public function showBikeTracking(BikeRental $rental)
    {
        // Authorize: only the renter or admin can view
        if ($rental->user_id !== auth()->id() && auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if rental is active
        if ($rental->status !== 'active') {
            return redirect()->route('user.bike-rentals.index')
                ->with('error', 'Tracking is only available for active rentals.');
        }
        
        $bike = $rental->bike;
        return view('tracking.bike', compact('rental', 'bike'));
    }
    
    /**
     * Admin live tracking page for all active bikes
     */
    public function adminBikeTracking()
    {
    $activeRentals = BikeRental::where('status', 'active')
        ->with(['bike', 'user'])
        ->get();
    
    return view('admin.live-tracking.bikes', compact('activeRentals'));
    }
    
    /**
     * Get all active bike rentals for admin map
     */
    public function getActiveBikeRentals()
    {
    $rentals = BikeRental::where('status', 'active')
        ->with(['bike', 'user'])
        ->get()
        ->map(function ($rental) {
            return [
                'bike_id' => $rental->bike_id,
                'bike_name' => $rental->bike->brand . ' ' . $rental->bike->model,
                'user_name' => $rental->user->name,
                'duration' => $rental->duration,
                'duration_type' => $rental->duration_type,
                'start_time' => $rental->created_at->timestamp,
                'location' => $rental->bike->latestLocation,
            ];
        });
    
    return response()->json($rentals);
    }
}