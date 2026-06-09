<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of vehicles.
     */
    public function index()
    {
        $vehicles = Vehicle::with('owner')->latest()->paginate(15);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle)
    {
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Approve the specified vehicle.
     */
    public function approve(Vehicle $vehicle)
    {
        $vehicle->update([
            'is_approved' => true,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
        
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle approved successfully.');
    }

    /**
     * Reject the specified vehicle.
     */
    public function reject(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $vehicle->update([
            'is_approved' => false,
            'approved_at' => null,
            'rejection_reason' => $request->reason,
        ]);
        
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle rejected successfully.');
    }

    /**
     * Remove the specified vehicle from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        
        return redirect()->route('admin.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }
}