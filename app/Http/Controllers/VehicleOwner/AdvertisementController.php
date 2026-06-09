<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdvertisementController extends Controller
{
    /**
     * READ: Display owner's advertisements
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->advertisements()->with('vehicle');
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('ad_type', $request->type);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('from_location', 'like', '%' . $request->search . '%')
                  ->orWhere('to_location', 'like', '%' . $request->search . '%');
            });
        }
        
        $advertisements = $query->latest()->paginate(10);
        
        // Get statistics
        $stats = [
            'total' => $user->advertisements()->count(),
            'pending' => $user->advertisements()->where('status', 'pending')->count(),
            'approved' => $user->advertisements()->where('status', 'approved')->count(),
            'rejected' => $user->advertisements()->where('status', 'rejected')->count(),
            'active' => $user->advertisements()
                ->where('status', 'approved')
                ->where('departure_time', '>', now())
                ->count(),
            'expired' => $user->advertisements()
                ->where('status', 'approved')
                ->where('departure_time', '<', now())
                ->count(),
        ];
        
        return view('vehicle-owner.advertisements.index', compact('advertisements', 'stats'));
    }

    /**
     * CREATE: Show create form
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
        
        return view('vehicle-owner.advertisements.create', compact('vehicles'));
    }

    /**
     * STORE: Save new advertisement
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20|max:2000',
            'ad_type' => 'required|in:ride_share,taxi,bus,bike_share',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'nullable|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'available_seats' => 'required|integer|min:0|lte:total_seats',
            'route_points' => 'nullable|json',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vehicle = Vehicle::find($request->vehicle_id);
        
        // If vehicle is already approved by admin, auto-approve the advertisement
        // If vehicle is not approved, advertisement goes to pending
        $status = $vehicle->is_approved ? 'approved' : 'pending';
        
        // Create slug from title
        $slug = Str::slug($request->title) . '-' . uniqid();

        // Create advertisement
        $advertisement = VehicleAdvertisement::create([
            'vehicle_id' => $request->vehicle_id,
            'owner_id' => $user->id,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'ad_type' => $request->ad_type,
            'from_location' => $request->from_location,
            'to_location' => $request->to_location,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'price' => $request->price,
            'total_seats' => $request->total_seats,
            'available_seats' => $request->available_seats,
            'route_points' => $request->route_points ? json_decode($request->route_points, true) : null,
            'amenities' => $request->amenities,
            'status' => $status,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('advertisements/' . $advertisement->id, 'public');
                $images[] = $path;
            }
            $advertisement->update(['images' => $images]);
        }

        $message = $status === 'approved' 
            ? 'Advertisement published immediately!' 
            : 'Advertisement created. Waiting for vehicle approval first.';

        return redirect()->route('vehicle-owner.advertisements.index')
            ->with('success', $message);
    }

    /**
     * SHOW: Display single advertisement
     */
    public function show(VehicleAdvertisement $advertisement)
    {
        // Ensure ownership
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $advertisement->load(['vehicle', 'bookings.user']);

        return view('vehicle-owner.advertisements.show', compact('advertisement'));
    }

    /**
     * EDIT: Show edit form
     */
    public function edit(VehicleAdvertisement $advertisement)
    {
        // Ensure ownership
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Don't allow editing approved ads with bookings
        if ($advertisement->status === 'approved' && $advertisement->bookings()->exists()) {
            return redirect()->route('vehicle-owner.advertisements.index')
                ->with('error', 'Cannot edit advertisement with existing bookings.');
        }

        $user = Auth::user();
        $vehicles = $user->vehicles()->where('is_approved', true)->get();

        return view('vehicle-owner.advertisements.edit', compact('advertisement', 'vehicles'));
    }

    /**
     * UPDATE: Update advertisement
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20|max:2000',
            'ad_type' => 'required|in:ride_share,taxi,bus,bike_share',
            'from_location' => 'required|string|max:255',
            'to_location' => 'required|string|max:255',
            'departure_time' => 'required|date|after:now',
            'arrival_time' => 'nullable|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'total_seats' => 'required|integer|min:1',
            'available_seats' => 'required|integer|min:0|lte:total_seats',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Update advertisement
        $advertisement->update([
            'vehicle_id' => $request->vehicle_id,
            'title' => $request->title,
            'description' => $request->description,
            'ad_type' => $request->ad_type,
            'from_location' => $request->from_location,
            'to_location' => $request->to_location,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'price' => $request->price,
            'total_seats' => $request->total_seats,
            'available_seats' => $request->available_seats,
            // Reset status to pending for review
            'status' => 'pending',
        ]);

        return redirect()->route('vehicle-owner.advertisements.index')
            ->with('success', 'Advertisement updated successfully. Pending re-approval.');
    }

    /**
     * DELETE: Remove advertisement
     */
    public function destroy(VehicleAdvertisement $advertisement)
    {
        // Ensure ownership
        if ($advertisement->owner_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check for bookings
        if ($advertisement->bookings()->exists()) {
            return back()->with('error', 'Cannot delete advertisement with bookings.');
        }

        $advertisement->delete();

        return redirect()->route('vehicle-owner.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }

    /**
     * CUSTOM: Duplicate advertisement
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
     * CUSTOM: Get statistics
     */
    public function statistics()
    {
        $user = Auth::user();
        
        $stats = [
            'views' => $user->advertisements()->sum('view_count'),
            'bookings' => $user->advertisements()->withCount('bookings')->get()->sum('bookings_count'),
            'revenue' => 0, // Calculate from completed bookings
        ];

        return response()->json($stats);
    }
}