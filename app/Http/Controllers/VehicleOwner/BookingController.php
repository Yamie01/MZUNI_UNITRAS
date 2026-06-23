<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    protected $revenueService;

    public function __construct(RevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    /**
     * Display a list of bookings for the authenticated vehicle owner.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Booking::whereHas('advertisement', function ($q) use ($user) {
            $q->where('owner_id', $user->id);
        })->with(['user', 'advertisement', 'vehicle']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('trip_status')) {
            $query->where('trip_status', $request->trip_status);
        }

        $bookings = $query->latest()->paginate(15);

        $stats = [
            'total'        => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->count(),
            'pending'      => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('status', 'pending')->count(),
            'confirmed'    => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('status', 'confirmed')->count(),
            'completed'    => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('trip_status', 'completed')->count(),
            'in_progress'  => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('trip_status', 'in_progress')->count(),
            'total_revenue'=> Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))
                ->where('trip_status', 'completed')
                ->sum('owner_earnings'),
        ];

        return view('vehicle-owner.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Display a single booking.
     */
    public function show(Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['user', 'advertisement', 'vehicle']);

        return view('vehicle-owner.bookings.show', compact('booking'));
    }

    /**
     * Update booking status (confirm, cancel, or complete).
     */
    public function update(Request $request, Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
        ]);

        DB::transaction(function () use ($request, $booking) {
            $oldStatus = $booking->status;
            $newStatus = $request->status;

            // If cancelling a confirmed booking, restore seats
            if ($newStatus === 'cancelled' && $oldStatus === 'confirmed') {
                $booking->advertisement->increment('available_seats', $booking->number_of_seats);
            }

            $booking->update(['status' => $newStatus]);
        });

        return redirect()->route('vehicle-owner.bookings.index')
            ->with('success', "Booking #{$booking->id} marked as {$request->status}.");
    }

    /**
     * Start the trip (owner marks as in transit).
     */
    /**
 * Start a trip (vehicle owner starts the ride).
 */
public function startTrip(Booking $booking)
{
    // Only the vehicle owner can start the trip from their panel
    if ($booking->advertisement->owner_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    if ($booking->status !== 'confirmed') {
        return back()->with('error', 'Booking must be confirmed to start trip.');
    }

    $booking->update([
        'trip_status' => 'in_progress',
        'trip_started_at' => now(),
    ]);

    return back()->with('success', 'Trip started. Passenger has been notified.');
}

/**
 * Complete a trip (vehicle owner ends the ride).
 */
public function completeTrip(Booking $booking)
{
    if ($booking->advertisement->owner_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    if ($booking->trip_status !== 'in_progress') {
        return back()->with('error', 'Trip must be in progress to complete.');
    }

    DB::transaction(function () use ($booking) {
        if ($booking->platform_fee === null) {
            $booking->platform_fee = $booking->total_price * 0.20;
            $booking->owner_earnings = $booking->total_price * 0.80;
        }

        $booking->update([
            'trip_status' => 'completed',
            'trip_completed_at' => now(),
            'status' => 'completed',
        ]);
    });

    return back()->with('success', 'Trip completed. Earnings have been credited.');
}

}