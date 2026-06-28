<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    /**
     * Display owner's advertisements with filters and stats.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = $user->advertisements()->with('vehicle');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('ad_type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('fromLocation', fn($q) => $q->where('name', 'like', '%' . $search . '%'))
                    ->orWhereHas('toLocation', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
            });
        }

        $advertisements = $query->latest()->paginate(10);

        $stats = [
            'total'    => $user->advertisements()->count(),
            'pending'  => $user->advertisements()->where('status', 'pending')->count(),
            'approved' => $user->advertisements()->where('status', 'approved')->count(),
            'rejected' => $user->advertisements()->where('status', 'rejected')->count(),
            'active'   => $user->advertisements()
                ->where('status', 'approved')
                ->where('departure_time', '>', now())
                ->count(),
            'expired'  => $user->advertisements()
                ->where('status', 'approved')
                ->where('departure_time', '<', now())
                ->count(),
        ];

        return view('vehicle-owner.advertisements.index', compact('advertisements', 'stats'));
    }

    /**
     * Show the form to create a new advertisement.
     */
    /**
 * Show the form for creating a new advertisement.
 */
public function create()
{
    $user = Auth::user();
    
    // Get only approved vehicles
    $vehicles = $user->vehicles()
        ->where('is_approved', true)
        ->where('status', 'available')
        ->get();
    
    if ($vehicles->isEmpty()) {
        return redirect()->route('vehicle-owner.vehicles.index')
            ->with('error', 'You need at least one approved vehicle to create an advertisement.');
    }
    
    // Fetch locations for dropdown
    $locations = \App\Models\Location::orderBy('name')->get();
    
    return view('vehicle-owner.advertisements.create', compact('vehicles', 'locations'));
}

    /**
     * Store a new advertisement.
     */
    public function store(Request $request)
{
    $user = Auth::user();
    
    $validator = Validator::make($request->all(), [
        'vehicle_id' => [
            'required',
            'exists:vehicles,id',
            function ($attribute, $value, $fail) use ($user) {
                $vehicle = Vehicle::find($value);
                if (!$vehicle || $vehicle->owner_id !== $user->id) {
                    $fail('Invalid vehicle selected.');
                }
                if ($vehicle && !$vehicle->is_approved) {
                    $fail('Vehicle must be approved first.');
                }
            },
        ],
        // REMOVE title and description
        // 'title' => 'required|string|max:255',
        // 'description' => 'required|string|min:20|max:2000',
        'ad_type' => 'required|in:ride_share,taxi,bus,bike_share',
        'route' => 'required|string|max:255', // ← ADD THIS
        'from_location_id' => 'required|exists:locations,id',
        'to_location_id' => 'required|exists:locations,id',
        'departure_time' => 'required|date|after:now',
        'price' => 'required|numeric|min:0',
        'total_seats' => 'required|integer|min:1',
        'available_seats' => 'required|integer|min:0|lte:total_seats',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Get the vehicle
    $vehicle = Vehicle::find($request->vehicle_id);
    
    // Determine status
    $status = $vehicle->is_approved ? 'approved' : 'pending';
    
    // Create slug from route (NEW)
    $slug = Str::slug($request->route) . '-' . uniqid();

    // Create advertisement
    $advertisement = VehicleAdvertisement::create([
        'vehicle_id' => $request->vehicle_id,
        'owner_id' => $user->id,
        'title' => $request->route, // Use route as title
        'slug' => $slug,
        'description' => 'Ride from ' . $request->from_location_id . ' to ' . $request->to_location_id,
        'ad_type' => $request->ad_type,
        'from_location_id' => $request->from_location_id,
        'to_location_id' => $request->to_location_id,
        'departure_time' => $request->departure_time,
        'price' => $request->price,
        'total_seats' => $request->total_seats,
        'available_seats' => $request->available_seats,
        'status' => $status,
        'route' => $request->route, // Store the route
    ]);

    // Handle image upload
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('advertisements/' . $advertisement->id, 'public');
        $advertisement->update(['image' => $path]);
    }

    $message = $status === 'approved' 
        ? 'Ride published immediately!' 
        : 'Ride created. Waiting for approval.';

    return redirect()->route('vehicle-owner.advertisements.index')
        ->with('success', $message);
}
    /**
     * Display a single advertisement.
     */
    public function show(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $advertisement->load(['vehicle', 'bookings.user', 'fromLocation', 'toLocation']);

        return view('vehicle-owner.advertisements.show', compact('advertisement'));
    }

    /**
     * Show the form to edit an advertisement.
     */
    public function edit(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($advertisement->status === 'approved' && $advertisement->bookings()->exists()) {
            return redirect()->route('vehicle-owner.advertisements.index')
                ->with('error', 'Cannot edit advertisement with existing bookings.');
        }

        $user = Auth::user();
        $vehicles = $user->vehicles()->where('is_approved', true)->get();
        $locations = Location::orderBy('name')->get();

        return view('vehicle-owner.advertisements.edit', compact('advertisement', 'vehicles', 'locations'));
    }

    /**
     * Update an advertisement.
     */
    public function update(Request $request, VehicleAdvertisement $advertisement)
{
    // Ensure ownership
    if ($advertisement->owner_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    $validator = Validator::make($request->all(), [
        'vehicle_id' => [
            'required',
            'exists:vehicles,id',
            function ($attribute, $value, $fail) {
                $vehicle = Vehicle::find($value);
                if ($vehicle && !$vehicle->is_approved) {
                    $fail('Vehicle must be approved first.');
                }
            },
        ],
        // REMOVE title and description
        'route' => 'required|string|max:255',
        'ad_type' => 'required|in:ride_share,taxi,bus,bike_share',
        'from_location_id' => 'required|exists:locations,id',
        'to_location_id' => 'required|exists:locations,id',
        'departure_time' => 'required|date|after:now',
        'price' => 'required|numeric|min:0',
        'total_seats' => 'required|integer|min:1',
        'available_seats' => 'required|integer|min:0|lte:total_seats',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $advertisement->update([
        'vehicle_id' => $request->vehicle_id,
        'title' => $request->route,
        'route' => $request->route,
        'ad_type' => $request->ad_type,
        'from_location_id' => $request->from_location_id,
        'to_location_id' => $request->to_location_id,
        'departure_time' => $request->departure_time,
        'price' => $request->price,
        'total_seats' => $request->total_seats,
        'available_seats' => $request->available_seats,
        'status' => 'pending',
    ]);

    return redirect()->route('vehicle-owner.advertisements.index')
        ->with('success', 'Advertisement updated successfully. Pending re-approval.');
}

    /**
     * Delete an advertisement.
     */
    public function destroy(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($advertisement->bookings()->exists()) {
            return back()->with('error', 'Cannot delete advertisement with bookings.');
        }

        $advertisement->delete();

        return redirect()->route('vehicle-owner.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }

    /**
     * Duplicate an advertisement.
     */
    public function duplicate(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        $newAdvertisement = $advertisement->replicate();
        $newAdvertisement->title = $advertisement->title . ' (Copy)';
        $newAdvertisement->slug = Str::slug($newAdvertisement->title) . '-' . uniqid();
        $newAdvertisement->status = 'pending';
        $newAdvertisement->created_at = now();
        $newAdvertisement->save();

        return redirect()->route('vehicle-owner.advertisements.edit', $newAdvertisement)
            ->with('success', 'Advertisement duplicated. Please review and submit.');
    }

    /**
     * Get statistics (JSON).
     */
    public function statistics()
    {
        $user = Auth::user();

        $stats = [
            'views'    => $user->advertisements()->sum('view_count'),
            'bookings' => $user->advertisements()->withCount('bookings')->get()->sum('bookings_count'),
            'revenue'  => 0,
        ];

        return response()->json($stats);
    }

    /**
     * Start a trip for an advertisement.
     */
    public function startTrip(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($advertisement->trip_status === 'completed') {
            return back()->with('error', 'This trip is already completed.');
        }

        $advertisement->update([
            'trip_status' => 'in_progress',
            'trip_started_at' => now(),
        ]);

        return back()->with('success', 'Trip started! Riders can now track you via GPS.');
    }

    /**
     * Complete a trip for an advertisement.
     */
    public function completeTrip(VehicleAdvertisement $advertisement)
    {
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($advertisement->trip_status !== 'in_progress') {
            return back()->with('error', 'Trip must be in progress to complete.');
        }

        $advertisement->update([
            'trip_status' => 'completed',
            'trip_completed_at' => now(),
        ]);

        return back()->with('success', 'Trip completed. This ride has been removed from listings.');
    }
}