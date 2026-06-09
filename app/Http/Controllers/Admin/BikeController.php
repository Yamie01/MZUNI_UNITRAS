<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BikeController extends Controller
{
    /**
     * Display a listing of bikes.
     */
    public function index(Request $request)
    {
        $query = Bike::query();
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('bike_code', 'like', '%' . $request->search . '%')
                  ->orWhere('brand', 'like', '%' . $request->search . '%')
                  ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }
        
        $bikes = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Bike::count(),
            'available' => Bike::where('status', 'available')->count(),
            'rented' => Bike::where('status', 'rented')->count(),
            'maintenance' => Bike::where('status', 'maintenance')->count(),
            'total_revenue' => Bike::sum('total_revenue'),
            'total_rentals' => Bike::sum('total_rentals'),
        ];
        
        return view('admin.bikes.index', compact('bikes', 'stats'));
    }

    /**
     * Show form to create new bike.
     */
    public function create()
    {
        return view('admin.bikes.create');
    }

    /**
     * Store a newly created bike.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bike_code' => 'required|string|unique:bikes|max:50',
            'brand' => 'required|string|max:100',
            'model' => 'required|string|max:100',
            'type' => 'required|in:mountain,road,hybrid,electric,city',
            'color' => 'nullable|string|max:50',
            'year' => 'nullable|integer|min:2000|max:' . date('Y'),
            'price_per_hour' => 'required|numeric|min:0',
            'price_per_day' => 'required|numeric|min:0',
            'deposit_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'features' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bike = Bike::create([
            'bike_code' => strtoupper($request->bike_code),
            'brand' => $request->brand,
            'model' => $request->model,
            'type' => $request->type,
            'color' => $request->color,
            'year' => $request->year,
            'price_per_hour' => $request->price_per_hour,
            'price_per_day' => $request->price_per_day,
            'deposit_amount' => $request->deposit_amount,
            'description' => $request->description,
            'features' => $request->features,
            'status' => 'available',
            'is_active' => true,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('bikes/' . $bike->id, 'public');
                $images[] = $path;
            }
            $bike->update(['images' => $images]);
        }

        return redirect()->route('admin.bikes.index')
            ->with('success', 'Bike added successfully.');
    }

    /**
     * Display bike details.
     */
    public function show(Bike $bike)
    {
        $rentals = $bike->rentals()->latest()->paginate(10);
        return view('admin.bikes.show', compact('bike', 'rentals'));
    }

    /**
     * Show edit form.
     */
    public function edit(Bike $bike)
    {
        return view('admin.bikes.edit', compact('bike'));
    }

    /**
     * Update bike.
     */
    public function update(Request $request, Bike $bike)
{
    $request->validate([
        'bike_code' => 'required|string|unique:bikes,bike_code,' . $bike->id,
        'brand' => 'required|string',
        'model' => 'required|string',
        'type' => 'required|string',
        'price_per_hour' => 'required|numeric|min:0',
        'price_per_day' => 'required|numeric|min:0',
        'deposit_amount' => 'required|numeric|min:0',
        'status' => 'required|in:available,rented,maintenance,out_of_service',
        'is_active' => 'boolean',
    ]);

    $data = $request->except(['images', 'features']);
    
    // Handle features as JSON
    if ($request->has('features')) {
        $data['features'] = json_encode($request->features);
    }
    
    $bike->update($data);
    
    // Handle images if uploaded
    if ($request->hasFile('images')) {
            $images = $bike->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('bikes/' . $bike->id, 'public');
                $images[] = $path;
            }
            $bike->update(['images' => $images]);
        }
    
    return redirect()->route('admin.bikes.index')
        ->with('success', 'Bike updated successfully.');
    }
    /**
     * Delete bike.
     */
    public function destroy(Bike $bike)
    {
        // Check if bike has active rentals
        if ($bike->activeRentals()->exists()) {
            return back()->with('error', 'Cannot delete bike with active rentals.');
        }
        
        $bike->delete();
        return redirect()->route('admin.bikes.index')
            ->with('success', 'Bike deleted successfully.');
    }

    /**
     * Update bike status.
     */
    public function updateStatus(Request $request, Bike $bike)
    {
        $request->validate([
            'status' => 'required|in:available,maintenance,out_of_service'
        ]);

        $bike->update(['status' => $request->status]);
        
        return back()->with('success', 'Bike status updated.');
    }

    /**
     * Generate QR code for bike.
     */
    public function generateQR(Bike $bike)
    {
        // Generate unique QR code
        $qrCode = 'BIKE-' . $bike->id . '-' . Str::random(8);
        $bike->update(['qr_code' => $qrCode]);
        
        return back()->with('success', 'QR code generated.');
    }
}