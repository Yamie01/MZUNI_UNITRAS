<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * READ: Display owner's vehicles
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->vehicles();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by approval
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->approved === 'yes');
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('registration_number', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }
        
        $vehicles = $query->latest()->paginate(10);
        
        // Get statistics
        $stats = [
            'total' => $user->vehicles()->count(),
            'approved' => $user->vehicles()->where('is_approved', true)->count(),
            'pending' => $user->vehicles()->where('is_approved', false)->count(),
            'available' => $user->vehicles()->where('status', 'available')->count(),
            'booked' => $user->vehicles()->where('status', 'booked')->count(),
        ];
        
        return view('vehicle-owner.vehicles.index', compact('vehicles', 'stats'));
    }

    /**
     * CREATE: Show create form
     */
    public function create()
    {
        return view('vehicle-owner.vehicles.create');
    }

    /**
     * STORE: Save new vehicle
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|in:bike,car,taxi,bus,minibus',
            'registration_number' => 'required|string|unique:vehicles|max:20',
            'make' => 'nullable|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'color' => 'nullable|string|max:30',
            'capacity' => 'required|integer|min:1|max:100',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid',
            'price_per_km' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'insurance_number' => 'nullable|string|max:50',
            'insurance_expiry' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create vehicle
        $vehicle = Auth::user()->vehicles()->create([
            'vehicle_type' => $request->vehicle_type,
            'registration_number' => strtoupper($request->registration_number),
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'capacity' => $request->capacity,
            'fuel_type' => $request->fuel_type,
            'price_per_km' => $request->price_per_km,
            'price_per_day' => $request->price_per_day,
            'insurance_number' => $request->insurance_number,
            'insurance_expiry' => $request->insurance_expiry,
            'description' => $request->description,
            'status' => 'available',
            'is_approved' => false,
        ]);

        // Handle image upload (if any)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('vehicles/' . $vehicle->id, 'public');
                // Save path to vehicle_images table (you may need to create this)
                // $vehicle->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('vehicle-owner.vehicles.index')
            ->with('success', 'Vehicle added successfully. Awaiting admin approval.');
    }

    /**
     * EDIT: Show edit form
     */
    public function edit(Vehicle $vehicle)
    {
        // Ensure ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('vehicle-owner.vehicles.edit', compact('vehicle'));
    }

    /**
     * UPDATE: Update vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        // Ensure ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|in:bike,car,taxi,bus,minibus',
            'registration_number' => 'required|string|unique:vehicles,registration_number,' . $vehicle->id,
            'make' => 'nullable|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'color' => 'nullable|string|max:30',
            'capacity' => 'required|integer|min:1|max:100',
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid',
            'price_per_km' => 'nullable|numeric|min:0',
            'price_per_day' => 'nullable|numeric|min:0',
            'insurance_number' => 'nullable|string|max:50',
            'insurance_expiry' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update vehicle
        $vehicle->update([
            'vehicle_type' => $request->vehicle_type,
            'registration_number' => strtoupper($request->registration_number),
            'make' => $request->make,
            'model' => $request->model,
            'year' => $request->year,
            'color' => $request->color,
            'capacity' => $request->capacity,
            'fuel_type' => $request->fuel_type,
            'price_per_km' => $request->price_per_km,
            'price_per_day' => $request->price_per_day,
            'insurance_number' => $request->insurance_number,
            'insurance_expiry' => $request->insurance_expiry,
            'description' => $request->description,
            // Reset approval status if important info changed
            'is_approved' => false,
        ]);

        return redirect()->route('vehicle-owner.vehicles.index')
            ->with('success', 'Vehicle updated successfully. Needs re-approval.');
    }

    /**
     * DELETE: Remove vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        // Ensure ownership
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check for active advertisements
        if ($vehicle->advertisements()->where('status', 'approved')->exists()) {
            return back()->with('error', 'Cannot delete vehicle with active advertisements.');
        }

        // Check for upcoming bookings
        if ($vehicle->bookings()->where('status', 'confirmed')
            ->whereHas('advertisement', function($q) {
                $q->where('departure_time', '>', now());
            })->exists()) {
            return back()->with('error', 'Cannot delete vehicle with upcoming bookings.');
        }

        $vehicle->delete();

        return redirect()->route('vehicle-owner.vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    /**
     * CUSTOM: Toggle vehicle status
     */
    public function toggleStatus(Vehicle $vehicle)
    {
        if ($vehicle->owner_id !== Auth::id()) {
            abort(403);
        }

        $newStatus = $vehicle->status === 'available' ? 'maintenance' : 'available';
        $vehicle->update(['status' => $newStatus]);

        return back()->with('success', 'Vehicle status updated.');
    }
}