<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\VehicleAdvertisement;
use App\Models\Bike;
use App\Models\Review;
use App\Models\Message;
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
        $pendingReviews = 0; // Placeholder until review system is implemented

        $unreadMessages = 0;
        if (class_exists(\App\Models\Message::class)) {
            $unreadMessages = Message::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        $pendingPayments = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count() + BikeRental::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // ---------- PROFILE STRENGTH ----------
        $profileFields = ['phone', 'address', 'profile_photo'];
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

        // ---------- SPENDING STATISTICS ----------
        $totalSpentOnRides = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');

        $totalSpentOnRentals = BikeRental::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalSpent = $totalSpentOnRides + $totalSpentOnRentals;

        $totalRides = Booking::where('user_id', $user->id)->count();
        $totalRentals = BikeRental::where('user_id', $user->id)->count();

        $moneySaved = Booking::where('user_id', $user->id)
            ->where('booking_type', 'subscription')
            ->sum('subtotal') ?? 0;

        // ---------- OLD STATISTICS (for backward compatibility) ----------
        $totalBikeRentals = $totalRentals;

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
            'missingFields',
            'moneySaved'
        ));
    }
}