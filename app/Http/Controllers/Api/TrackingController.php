<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleLocation;
use App\Models\Vehicle;
use App\Models\Bike;
use App\Models\Booking;
use App\Models\BikeRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{
    /**
     * Update vehicle location (driver)
     */
    public function updateVehicleLocation(Request $request, Vehicle $vehicle)
    {
        // Authorize: only owner or admin
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

        // Broadcast event (will be done via WebSocket)

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }

    /**
     * Update bike location (renter)
     */
    public function updateBikeLocation(Request $request, Bike $bike)
    {
        // Check if user has an active rental for this bike
        $rental = BikeRental::where('bike_id', $bike->id)
            ->where('status', 'active')
            ->where('user_id', Auth::id())
            ->first();

        if (!$rental && Auth::user()->user_type !== 'admin') {
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

        return response()->json([
            'success' => true,
            'location' => $location,
        ]);
    }

    /**
     * Get tracking info for a ride (passenger view)
     */
    public function getRideTracking($bookingId)
    {
        $booking = Booking::with(['vehicle.latestLocation'])
            ->where('user_id', Auth::id())
            ->findOrFail($bookingId);

        if ($booking->status !== 'confirmed' && $booking->trip_status !== 'in_progress') {
            return response()->json(['error' => 'Tracking not available'], 400);
        }

        return response()->json([
            'booking' => $booking,
            'location' => $booking->vehicle->latestLocation,
        ]);
    }

    /**
     * Get tracking info for a bike (renter view)
     */
    public function getBikeTracking($rentalId)
    {
        $rental = BikeRental::with(['bike.latestLocation'])
            ->where('user_id', Auth::id())
            ->findOrFail($rentalId);

        if ($rental->status !== 'active') {
            return response()->json(['error' => 'Tracking not available'], 400);
        }

        return response()->json([
            'rental' => $rental,
            'location' => $rental->bike->latestLocation,
        ]);
    }
}