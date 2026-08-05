<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Services\VehicleVettingService;
use Illuminate\Http\Request;

class VehicleVettingController extends Controller
{
    protected $vettingService;

    public function __construct(VehicleVettingService $vettingService)
    {
        $this->vettingService = $vettingService;
    }

    public function index()
    {
        $pendingVehicles = Vehicle::pendingVetting()->with('owner')->get();
        $manualReviewVehicles = Vehicle::manualReview()->with('owner')->get();
        $rejectedVehicles = Vehicle::rejected()->with('owner')->get();
        $vettedVehicles = Vehicle::vetted()->with('owner')->get();

        return view('admin.vehicles.vetting', compact(
            'pendingVehicles',
            'manualReviewVehicles',
            'rejectedVehicles',
            'vettedVehicles'
        ));
    }

    public function show(Vehicle $vehicle)
    {
        return view('admin.vehicles.vetting-detail', compact('vehicle'));
    }

    public function approve(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $this->vettingService->approveManually($vehicle, auth()->id(), $request->notes);

        return redirect()->route('admin.vetting.index')
            ->with('success', 'Vehicle approved successfully.');
    }

    public function reject(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $this->vettingService->rejectManually($vehicle, auth()->id(), $request->reason);

        return redirect()->route('admin.vetting.index')
            ->with('success', 'Vehicle rejected successfully.');
    }

    public function revet(Vehicle $vehicle)
    {
        $result = $this->vettingService->revet($vehicle);

        return redirect()->route('admin.vetting.show', $vehicle)
            ->with('success', 'Vehicle re-vetted automatically.');
    }

    public function bulkVet()
    {
        $results = $this->vettingService->vetAllPending();

        $approved = collect($results)->filter(fn($r) => $r['status'] === 'approved')->count();
        $manual = collect($results)->filter(fn($r) => $r['status'] === 'manual_review')->count();
        $rejected = collect($results)->filter(fn($r) => $r['status'] === 'rejected')->count();

        return redirect()->route('admin.vetting.index')
            ->with('success', "Bulk vetting complete: $approved approved, $manual manual review, $rejected rejected.");
    }

    public function checkLicense(Request $request)
    {
        $request->validate(['license_plate' => 'required|string']);

        $exists = Vehicle::where('license_plate', $request->license_plate)->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Vehicle already registered' : 'Vehicle available',
        ]);
    }
}