<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BikeController extends Controller
{
    /**
     * Display available bikes.
     */
    public function index(Request $request)
    {
        $query = Bike::available();
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price_per_hour', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price_per_hour', '<=', $request->max_price);
        }
        
        $bikes = $query->paginate(12);
        
        return view('user.bikes.index', compact('bikes'));
    }

    /**
     * Show bike details.
     */
    public function show(Bike $bike)
    {
        if (!$bike->is_active || $bike->status !== 'available') {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is not available.');
        }
        
        return view('user.bikes.show', compact('bike'));
    }

    /**
     * Show rental form.
     */
    public function rent(Bike $bike)
    {
        if (!$bike->is_active || $bike->status !== 'available') {
            return redirect()->route('user.bikes.index')
                ->with('error', 'This bike is not available for rent.');
        }
        
        return view('user.bikes.rent', compact('bike'));
    }

    /**
     * Process bike rental.
     */
    public function processRent(Request $request, Bike $bike)
    {
        // Validate the request
        $request->validate([
            'duration_type' => 'required|in:hourly,daily',
            'duration' => 'required|integer|min:1|max:72',
            'pickup_location' => 'required|string',
            'start_time' => 'required|date|after:now',
        ]);

        // Calculate rate based on duration type - FIXED: define the $rate variable
        if ($request->duration_type === 'hourly') {
            $rate = $bike->price_per_hour;
        } else {
            $rate = $bike->price_per_day;
        }
        
        // Calculate subtotal
        $subtotal = $rate * $request->duration;
        
        // Calculate total (subtotal + deposit)
        $total = $subtotal + $bike->deposit_amount;
        
        DB::transaction(function () use ($request, $bike, $rate, $subtotal, $total) {
            // Generate unique rental code
            $rentalCode = 'BIKE-' . strtoupper(uniqid());
            
            // Calculate end time
            $endTime = null;
            if ($request->duration_type === 'hourly') {
                $endTime = date('Y-m-d H:i:s', strtotime($request->start_time . ' + ' . $request->duration . ' hours'));
            } else {
                $endTime = date('Y-m-d H:i:s', strtotime($request->start_time . ' + ' . $request->duration . ' days'));
            }
            
            // Create rental record
            $rental = BikeRental::create([
                'rental_code' => $rentalCode,
                'bike_id' => $bike->id,
                'user_id' => auth()->id(),
                'start_time' => $request->start_time,
                'end_time' => $endTime,
                'duration_type' => $request->duration_type,
                'duration' => $request->duration,
                'rate_per_unit' => $rate,
                'subtotal' => $subtotal,
                'deposit_paid' => $bike->deposit_amount,
                'total_amount' => $total,
                'status' => 'pending',
                'pickup_location' => $request->pickup_location,
                'notes' => $request->notes,
            ]);
            
            // Update bike status to rented
            $bike->update(['status' => 'rented']);
        });
        
        return redirect()->route('user.bike-rentals.index')
            ->with('success', 'Rental created successfully! Please complete payment.');
    }

    /**
     * Display user's bike rentals.
     */
    public function myRentals()
    {
        $rentals = auth()->user()->bikeRentals()
            ->with('bike')
            ->latest()
            ->paginate(10);
        
        return view('user.bike-rentals.index', compact('rentals'));
    }

    /**
     * Display rental details.
     */
    public function rentalDetails(BikeRental $rental)
    {
        if ($rental->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('user.bike-rentals.show', compact('rental'));
    }
}