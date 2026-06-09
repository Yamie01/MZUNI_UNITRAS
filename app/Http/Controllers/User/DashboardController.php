<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\VehicleAdvertisement;
use App\Models\Bike;
use App\Models\Review;
use App\Models\Message;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ---------- AVAILABLE RIDES & BIKES ----------
        $availableRides = VehicleAdvertisement::with(['vehicle', 'owner'])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->limit(6)
            ->get();

        $availableBikes = Bike::where('status', 'available')
            ->where('is_active', true)
            ->limit(4)
            ->get();

        $availableRidesCount = $availableRides->count();
        $availableBikesCount = $availableBikes->count();

        // ---------- USER RATING ----------
        $userRating = Review::where('reviewee_id', $user->id)->avg('rating') ?? 4.8;

        // ---------- RECENT ACTIVITY ----------
        $recentBookings = Booking::with(['advertisement'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentBikeRentals = BikeRental::with(['bike'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $recentRentals = $recentBikeRentals; // alias for blade

        // ---------- PENDING ACTIONS ----------
        // TODO: Once you have a reviews table with booking_id and user_id, replace this with:
        // $pendingReviews = Booking::where('user_id', $user->id)
        //     ->where('status', 'completed')
        //     ->whereDoesntHave('reviews', fn($q) => $q->where('user_id', $user->id))
        //     ->count();

        // Safe fallback for now (no review system yet)
        $pendingReviews = 0;

        // Unread messages (if Message model exists)
        $unreadMessages = 0;
        if (class_exists(\App\Models\Message::class)) {
            $unreadMessages = Message::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        // Pending payments (bookings + rentals with status 'pending')
        $pendingPayments = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count() + BikeRental::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // ---------- PROFILE STRENGTH ----------
        $profileFields = ['phone', 'address', 'profile_photo']; // adjust to your users table columns
        $missing = 0;
        foreach ($profileFields as $field) {
            if (empty($user->$field)) $missing++;
        }
        $totalFields = count($profileFields);
        $profileCompletion = $totalFields > 0 ? round((($totalFields - $missing) / $totalFields) * 100) : 100;
        $missingFields = $missing;

        // ---------- FULL LISTS (paginated) ----------
        $allBookings = Booking::with(['advertisement'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $allBikeRentals = BikeRental::with(['bike'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // ---------- STATISTICS ----------
        $totalRides = Booking::where('user_id', $user->id)->count();
        $totalSpent = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');
        $totalBikeRentals = BikeRental::where('user_id', $user->id)->count();

        return view('user.dashboard', compact(
            'availableRides',
            'availableBikes',
            'availableRidesCount',
            'availableBikesCount',
            'userRating',
            'recentBookings',
            'recentBikeRentals',
            'recentRentals',
            'allBookings',
            'allBikeRentals',
            'totalRides',
            'totalSpent',
            'totalBikeRentals',
            'pendingReviews',
            'unreadMessages',
            'pendingPayments',
            'profileCompletion',
            'missingFields'
        ));
    }
}