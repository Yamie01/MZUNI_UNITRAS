<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OfferRideController extends Controller
{
    /**
     * Redirect to the offer ride page.
     * If user is not a vehicle owner, redirect to become one.
     * If user doesn't have an approved vehicle, redirect to add one.
     */
    public function index()
    {
        $user = Auth::user();
        
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
    }
}