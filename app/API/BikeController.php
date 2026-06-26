<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class BikeController extends Controller
{
    /**
     * Activate a bike with university ID and password.
     */
    public function activate(Request $request)
    {
        $request->validate([
            'bike_id' => 'required|exists:bikes,id',
            'university_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        // Verify university ID
        if ($user->university_id !== $request->university_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid university ID. Please check and try again.'
            ], 401);
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.'
            ], 401);
        }

        $bike = Bike::findOrFail($request->bike_id);

        // Check if bike is available
        if ($bike->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'This bike is not available for activation.'
            ], 400);
        }

        // Check if user has unpaid late fees
        if ($user->hasUnpaidLateFee()) {
            return response()->json([
                'success' => false,
                'message' => 'You have an unpaid late fee of MWK ' . number_format($user->getUnpaidLateFeeTotal(), 2) . '. Please pay it first.'
            ], 400);
        }

        try {
            // Create rental record
            $rental = BikeRental::create([
                'bike_id' => $bike->id,
                'user_id' => $user->id,
                'rental_code' => 'BIKE-' . strtoupper(uniqid()),
                'status' => 'active',
                'start_time' => now(),
                'is_paid' => false,
                'university_id' => $request->university_id,
                'rate_per_minute' => 2, // MWK 2 per minute
            ]);

            // Update bike status
            $bike->update(['status' => 'rented']);

            Log::info('Bike activated', [
                'bike_id' => $bike->id,
                'user_id' => $user->id,
                'rental_id' => $rental->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bike activated successfully! Your timer has started.',
                'rental' => [
                    'id' => $rental->id,
                    'start_time' => $rental->start_time,
                    'rate_per_minute' => 2,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Bike activation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate bike. Please try again.'
            ], 500);
        }
    }

    /**
     * Calculate bike rental cost.
     */
    public function calculateCost(BikeRental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }

        $minutes = now()->diffInMinutes($rental->start_time);
        $cost = $minutes * 2; // MWK 2 per minute

        return response()->json([
            'minutes' => $minutes,
            'cost' => $cost,
            'formatted_cost' => 'MWK ' . number_format($cost, 2),
        ]);
    }
}