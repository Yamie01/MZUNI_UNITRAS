<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bike;
use App\Models\BikeRental;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Review;
use App\Models\Subscription;
use App\Models\VehicleAdvertisement;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ============================================================
        // 1. AVAILABLE RIDES & BIKES (Exclude started/completed)
        // ============================================================
        $availableRides = VehicleAdvertisement::with(['vehicle', 'owner', 'fromLocation', 'toLocation'])
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->where('available_seats', '>', 0)
            ->where(function ($query) {
                $query->whereNull('trip_status')
                    ->orWhere('trip_status', 'scheduled');
            })
            ->orderBy('departure_time', 'asc')
            ->get();

        $availableBikes = Bike::where('status', 'available')
            ->where('is_active', true)
            ->get();

        $availableRidesCount = $availableRides->count();
        $availableBikesCount = $availableBikes->count();

        // ============================================================
        // 2. ACTIVE RENTAL (with fallback timestamps)
        // ============================================================
        $activeRental = BikeRental::with(['bike'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'rented'])
            ->first();

        // If timestamps are missing, compute them from created_at and duration
        if ($activeRental) {
            if (is_null($activeRental->start_time) && $activeRental->created_at) {
                $activeRental->start_time = $activeRental->created_at;
            }
            if (is_null($activeRental->expected_return_time) && $activeRental->created_at && $activeRental->duration) {
                $hours = $activeRental->duration_type == 'daily' ? $activeRental->duration * 24 : $activeRental->duration;
                $activeRental->expected_return_time = $activeRental->created_at->copy()->addHours($hours);
            }
        }

        // ============================================================
        // 3. USER RATING
        // ============================================================
        $userRating = Review::where('reviewee_id', $user->id)->avg('rating') ?? 4.8;

        // ============================================================
        // 4. RECENT ACTIVITY
        // ============================================================
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

        // ============================================================
        // 5. FULL LISTS (paginated)
        // ============================================================
        $allBookings = Booking::with(['advertisement', 'vehicle', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $allBikeRentals = BikeRental::with(['bike'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // ============================================================
        // 6. STATISTICS
        // ============================================================
        $totalSpentOnRides = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_price');

        $totalSpentOnRentals = BikeRental::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalSpent = $totalSpentOnRides + $totalSpentOnRentals;

        $totalRides = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalRentals = BikeRental::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalBikeRentals = $totalRentals; // alias for backward compatibility

        // Money saved from subscriptions
        $moneySaved = Booking::where('user_id', $user->id)
            ->where('booking_type', 'subscription')
            ->sum('subtotal') ?? 0;

        // ============================================================
        // 7. PENDING ACTIONS
        // ============================================================
        $pendingReviews = 0;
        // TODO: Uncomment when reviews table has booking_id
        // $pendingReviews = Booking::where('user_id', $user->id)
        //     ->where('status', 'completed')
        //     ->whereDoesntHave('reviews', fn($q) => $q->where('user_id', $user->id))
        //     ->count();

        // Unread messages (if Message model exists)
        $unreadMessages = 0;
        if (class_exists(Message::class)) {
            $unreadMessages = Message::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
        }

        $pendingPayments = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count() + BikeRental::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // ============================================================
        // 8. PROFILE STRENGTH
        // ============================================================
        $profileFields = ['phone', 'address', 'profile_photo'];
        $missing = 0;
        foreach ($profileFields as $field) {
            if (empty($user->$field)) $missing++;
        }
        $totalFields = count($profileFields);
        $profileCompletion = $totalFields > 0 ? round((($totalFields - $missing) / $totalFields) * 100) : 100;
        $missingFields = $missing;

        // ============================================================
        // 9. SUBSCRIPTION CHECK
        // ============================================================
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        // ============================================================
        // 10. RETURN VIEW
        // ============================================================
        return view('user.dashboard', compact(
            'availableRides',
            'availableBikes',
            'availableRidesCount',
            'availableBikesCount',
            'activeRental',
            'userRating',
            'recentBookings',
            'recentBikeRentals',
            'recentRentals',
            'allBookings',
            'allBikeRentals',
            'totalSpent',
            'totalRides',
            'totalRentals',
            'totalBikeRentals',
            'moneySaved',
            'pendingReviews',
            'unreadMessages',
            'pendingPayments',
            'profileCompletion',
            'missingFields',
            'subscription'
        ));
    }
}