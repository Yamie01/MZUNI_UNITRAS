<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleAdvertisement;
use Illuminate\Http\Request;

class RideController extends Controller
{
    /**
     * List all available rides
     */
    public function index(Request $request)
    {
        $query = VehicleAdvertisement::where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->with(['vehicle', 'owner']);

        // Optional filters
        if ($request->filled('from')) {
            $query->where('from_location', 'like', '%' . $request->from . '%');
        }
        if ($request->filled('to')) {
            $query->where('to_location', 'like', '%' . $request->to . '%');
        }
        if ($request->filled('date')) {
            $query->whereDate('departure_time', $request->date);
        }

        $rides = $query->orderBy('departure_time', 'asc')->paginate(15);

        return response()->json($rides);
    }

    /**
     * Show a specific ride
     */
    public function show($id)
    {
        $ride = VehicleAdvertisement::with(['vehicle', 'owner'])
            ->where('status', 'approved')
            ->findOrFail($id);

        return response()->json($ride);
    }
}