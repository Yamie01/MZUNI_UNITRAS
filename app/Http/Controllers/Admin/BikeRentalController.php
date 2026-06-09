<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use Illuminate\Http\Request;

class BikeRentalController extends Controller
{
    public function index(Request $request)
    {
        $query = BikeRental::with(['bike', 'user']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $rentals = $query->latest()->paginate(20);
        
        $stats = [
            'total' => BikeRental::count(),
            'active' => BikeRental::where('status', 'active')->count(),
            'completed' => BikeRental::where('status', 'completed')->count(),
            'cancelled' => BikeRental::where('status', 'cancelled')->count(),
            'total_revenue' => BikeRental::where('status', 'completed')->sum('total_amount'),
        ];
        
        return view('admin.bike-rentals.index', compact('rentals', 'stats'));
    }
    
    public function show(BikeRental $rental)
    {
        $rental->load(['bike', 'user']);
        return view('admin.bike-rentals.show', compact('rental'));
    }
    
    public function complete(BikeRental $rental, Request $request)
    {
    $request->validate([
        'damage_report' => 'nullable|string',
        'damage_charge' => 'nullable|numeric|min:0',
        'dropoff_location' => 'required|string',
    ]);

    $damageCharge = $request->damage_charge ?? 0;
    $lateFee = $rental->late_fee ?? 0;
    $totalDeduction = $damageCharge + $lateFee;
    $refundAmount = $rental->deposit_paid - $totalDeduction;
    
    $rental->update([
        'status' => 'completed',
        'actual_return_time' => now(),
        'damage_report' => $request->damage_report,
        'damage_charge' => $damageCharge,
        'late_fee' => $lateFee,
        'refund_amount' => max(0, $refundAmount),
        'dropoff_location' => $request->dropoff_location,
    ]);
    
    // Update bike statistics
    $rental->bike->increment('total_rentals');
    $rental->bike->increment('total_revenue', $rental->total_amount);
    
    // Make bike available
    $rental->bike->update(['status' => 'available']);
    
    $message = "Rental completed.\n";
    $message .= "Base Amount: MWK " . number_format($rental->total_amount, 2) . "\n";
    if ($lateFee > 0) {
        $message .= "Late Fee: MWK " . number_format($lateFee, 2) . "\n";
    }
    if ($damageCharge > 0) {
        $message .= "Damage Charge: MWK " . number_format($damageCharge, 2) . "\n";
    }
    $message .= "Refund: MWK " . number_format(max(0, $refundAmount), 2);
    
    return redirect()->route('admin.bike-rentals.index')
        ->with('success', nl2br($message));
    }
    public function cancel(BikeRental $rental)
    {
        if ($rental->status !== 'pending') {
            return back()->with('error', 'Cannot cancel active rental.');
        }
        
        $rental->update(['status' => 'cancelled']);
        $rental->bike->update(['status' => 'available']);
        
        return redirect()->route('admin.bike-rentals.index')
            ->with('success', 'Rental cancelled and bike is now available.');
    }
}