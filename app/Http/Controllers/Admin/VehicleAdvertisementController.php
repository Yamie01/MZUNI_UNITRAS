<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleAdvertisementController extends Controller
{
    /**
     * READ: Display all advertisements with filters
     */
    public function index(Request $request)
    {
        $query = VehicleAdvertisement::with(['vehicle', 'owner']);
        
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
        
        $advertisements = $query->latest()->paginate(15);
        
        // Get counts for dashboard
        $counts = [
            'total' => VehicleAdvertisement::count(),
            'pending' => VehicleAdvertisement::where('status', 'pending')->count(),
            'approved' => VehicleAdvertisement::where('status', 'approved')->count(),
            'rejected' => VehicleAdvertisement::where('status', 'rejected')->count(),
            'active' => VehicleAdvertisement::where('status', 'approved')
                ->where('departure_time', '>', now())
                ->count(),
        ];
        
        return view('admin.advertisements.index', compact('advertisements', 'counts'));
    }

    /**
     * SHOW: Display single advertisement
     */
    public function show(VehicleAdvertisement $advertisement)
    {
        $advertisement->load(['vehicle', 'owner', 'bookings.user']);
        
        return view('admin.advertisements.show', compact('advertisement'));
    }

    /**
     * CUSTOM: Approve advertisement
     */
    public function approve(VehicleAdvertisement $advertisement)
    {
        // Validate that vehicle exists and is approved
        if (!$advertisement->vehicle || !$advertisement->vehicle->is_approved) {
            return back()->with('error', 'Cannot approve: Vehicle is not approved yet.');
        }

        $advertisement->update([
            'status' => 'approved',
            'rejection_reason' => null
        ]);

        // Optional: Send notification to owner
        // $advertisement->owner->notify(new AdvertisementApproved($advertisement));

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement approved successfully.');
    }

    /**
     * CUSTOM: Reject advertisement with reason
     */
    public function reject(Request $request, VehicleAdvertisement $advertisement)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $advertisement->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Optional: Send notification to owner
        // $advertisement->owner->notify(new AdvertisementRejected($advertisement));

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement rejected successfully.');
    }

    /**
     * CUSTOM: Bulk approve selected advertisements
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:vehicle_advertisements,id'
        ]);

        VehicleAdvertisement::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => count($request->ids) . ' advertisements approved.'
        ]);
    }

    /**
     * DELETE: Remove advertisement
     */
    public function destroy(VehicleAdvertisement $advertisement)
    {
        // Check if there are bookings
        if ($advertisement->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete advertisement with existing bookings.');
        }

        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')
            ->with('success', 'Advertisement deleted successfully.');
    }

    /**
     * CUSTOM: Get statistics for dashboard
     */
    public function statistics()
    {
        $stats = [
            'total' => VehicleAdvertisement::count(),
            'pending' => VehicleAdvertisement::where('status', 'pending')->count(),
            'approved' => VehicleAdvertisement::where('status', 'approved')->count(),
            'rejected' => VehicleAdvertisement::where('status', 'rejected')->count(),
            
            'by_type' => [
                'ride_share' => VehicleAdvertisement::where('ad_type', 'ride_share')->count(),
                'taxi' => VehicleAdvertisement::where('ad_type', 'taxi')->count(),
                'bus' => VehicleAdvertisement::where('ad_type', 'bus')->count(),
                'bike_share' => VehicleAdvertisement::where('ad_type', 'bike_share')->count(),
            ],
            
            'upcoming' => VehicleAdvertisement::where('status', 'approved')
                ->where('departure_time', '>', now())
                ->count(),
            
            'expired' => VehicleAdvertisement::where('status', 'approved')
                ->where('departure_time', '<', now())
                ->count(),
        ];

        return response()->json($stats);
    }
}