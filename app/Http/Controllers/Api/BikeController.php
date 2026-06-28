<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use Illuminate\Http\Request;

class BikeController extends Controller
{
    /**
     * List all available bikes
     */
    public function index(Request $request)
    {
        $query = Bike::where('status', 'available')
            ->where('is_active', true);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $bikes = $query->paginate(12);

        return response()->json($bikes);
    }

    /**
     * Show a specific bike
     */
    public function show($id)
    {
        $bike = Bike::where('is_active', true)->findOrFail($id);
        return response()->json($bike);
    }
}