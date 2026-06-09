<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'advertisement', 'vehicle']);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Search by booking ID or user name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($userQuery) use ($request) {
                      $userQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $bookings = $query->latest()->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'total_revenue' => Booking::where('status', 'completed')->sum('total_price'),
        ];
        
        return view('admin.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'advertisement', 'vehicle', 'payment']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        // Check if booking is already completed
        if ($booking->status === 'completed') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Cannot delete a completed booking.');
        }
        
        // If booking is confirmed, restore seats before deleting
        if ($booking->status === 'confirmed') {
            $booking->advertisement->increment('available_seats', $booking->number_of_seats);
        }
        
        $booking->delete();
        
        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Export bookings to CSV.
     */
    public function export(Request $request)
    {
        $query = Booking::with(['user', 'advertisement']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $bookings = $query->get();
        
        $filename = 'bookings_' . date('Y-m-d') . '.csv';
        $handle = fopen($filename, 'w');
        
        // Add headers
        fputcsv($handle, [
            'Booking ID', 'Customer', 'Email', 'Phone', 'Ride Title', 
            'From', 'To', 'Seats', 'Total Price', 'Status', 'Booking Date'
        ]);
        
        // Add data
        foreach ($bookings as $booking) {
            fputcsv($handle, [
                $booking->id,
                $booking->user->name ?? 'N/A',
                $booking->user->email ?? 'N/A',
                $booking->user->phone ?? 'N/A',
                $booking->advertisement->title ?? 'N/A',
                $booking->advertisement->from_location ?? 'N/A',
                $booking->advertisement->to_location ?? 'N/A',
                $booking->number_of_seats,
                $booking->total_price,
                $booking->status,
                $booking->created_at->format('Y-m-d H:i')
            ]);
        }
        
        fclose($handle);
        
        return response()->download($filename)->deleteFileAfterSend();
    }
}