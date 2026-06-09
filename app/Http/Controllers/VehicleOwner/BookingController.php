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

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Booking::whereHas('advertisement', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })
            ->with(['user', 'advertisement', 'vehicle']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('trip_status')) {
            $query->where('trip_status', $request->trip_status);
        }
        
        $bookings = $query->latest()->paginate(15);
        
        $stats = [
            'total' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->count(),
            'pending' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('status', 'pending')->count(),
            'confirmed' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('status', 'confirmed')->count(),
            'completed' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('trip_status', 'completed')->count(),
            'in_progress' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))->where('trip_status', 'in_progress')->count(),
            'total_revenue' => Booking::whereHas('advertisement', fn($q) => $q->where('owner_id', $user->id))
                ->where('trip_status', 'completed')
                ->sum('owner_earnings'),
        ];
        
        return view('vehicle-owner.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load(['user', 'advertisement', 'vehicle']);
        
        return view('vehicle-owner.bookings.show', compact('booking'));
    }

    /**
     * Update booking status (confirm, cancel, complete)
     */
    public function update(Request $request, Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed'
        ]);

        DB::transaction(function () use ($request, $booking) {
            $oldStatus = $booking->status;
            $newStatus = $request->status;
            
            if ($newStatus === 'cancelled' && $oldStatus === 'confirmed') {
                $booking->advertisement->increment('available_seats', $booking->number_of_seats);
            }
            
            $booking->update(['status' => $newStatus]);
        });

        return redirect()->route('vehicle-owner.bookings.index')
            ->with('success', "Booking #{$booking->id} marked as {$request->status}.");
    }

    /**
     * Start a trip
     */
    public function startTrip(Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Booking must be confirmed before starting the trip.');
        }
        
        $booking->update([
            'trip_status' => 'in_progress',
            'trip_started_at' => now(),
        ]);
        
        return back()->with('success', 'Trip has been started. Safe journey!');
    }

    /**
     * Complete a trip and process revenue
     */
    public function completeTrip(Booking $booking)
    {
        if ($booking->advertisement->owner_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->trip_status !== 'in_progress') {
            return back()->with('error', 'Trip must be in progress to complete.');
        }
        
        DB::transaction(function () use ($booking) {
            // Process revenue split
            $split = $this->revenueService->processBookingRevenue($booking);
            
            // Update booking trip status
            $booking->update([
                'trip_status' => 'completed',
                'trip_completed_at' => now(),
                'status' => 'completed',
            ]);
        });
        
        $message = "Trip completed successfully!\n";
        $message .= "Total: MWK " . number_format($booking->total_price, 2) . "\n";
        $message .= "System Commission (15%): MWK " . number_format($booking->system_commission, 2) . "\n";
        $message .= "Your Earnings: MWK " . number_format($booking->owner_earnings, 2);
        
        return back()->with('success', $message);
    }
}