<?php

namespace App\Http\Controllers\VehicleOwner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    /**
     * Display earnings dashboard for vehicle owner.
     * Since payouts are automatic (direct), this is for tracking only.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->id;

        // ============================================================
        // 1. EARNINGS SUMMARY
        // ============================================================
        $totalEarnings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'completed')->sum('owner_earnings');

        $pendingEarnings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'confirmed')->sum('owner_earnings');

        $totalBookings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->count();

        $completedBookings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'completed')->count();

        // ============================================================
        // 2. PAYOUT SUMMARY (Direct Payouts)
        // ============================================================
        $payouts = Payout::where('user_id', $ownerId)->latest()->get();
        
        $totalPayouts = $payouts->where('status', 'completed')->sum('amount');
        $pendingPayouts = $payouts->where('status', 'pending')->sum('amount');
        $failedPayouts = $payouts->where('status', 'failed')->sum('amount');
        
        // Available balance (what's left after payouts)
        $availableBalance = $totalEarnings - $totalPayouts;

        // ============================================================
        // 3. MONTHLY EARNINGS (Last 12 Months)
        // ============================================================
        $monthlyEarnings = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $earnings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->where('status', 'completed')
                ->whereYear('trip_completed_at', $date->year)
                ->whereMonth('trip_completed_at', $date->month)
                ->sum('owner_earnings');
            $monthlyEarnings[$month] = $earnings;
        }

        // ============================================================
        // 4. MONTHLY PAYOUTS (Last 12 Months)
        // ============================================================
        $monthlyPayouts = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $payoutAmount = Payout::where('user_id', $ownerId)
                ->where('status', 'completed')
                ->whereYear('processed_at', $date->year)
                ->whereMonth('processed_at', $date->month)
                ->sum('amount');
            $monthlyPayouts[$month] = $payoutAmount;
        }

        // ============================================================
        // 5. PAYOUT HISTORY (Paginated)
        // ============================================================
        $payoutsPaginated = Payout::where('user_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // ============================================================
        // 6. EARNINGS BY VEHICLE
        // ============================================================
        $earningsByVehicle = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'completed')
            ->select('vehicle_id', DB::raw('SUM(owner_earnings) as total'))
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        // ============================================================
        // 7. RECENT PAYOUTS
        // ============================================================
        $recentPayouts = Payout::where('user_id', $ownerId)
            ->where('status', 'completed')
            ->orderBy('processed_at', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // 8. UPCOMING EARNINGS (Confirmed Bookings)
        // ============================================================
        $upcomingBookings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'confirmed')
            ->with(['advertisement', 'user'])
            ->orderBy('trip_date', 'asc')
            ->limit(10)
            ->get();

        // ============================================================
        // 9. FILTERS
        // ============================================================
        $filter = $request->input('filter', 'all');
        $dateRange = $request->input('date_range', 'this_month');

        return view('vehicle-owner.earnings.index', compact(
            'totalEarnings',
            'pendingEarnings',
            'totalBookings',
            'completedBookings',
            'totalPayouts',
            'pendingPayouts',
            'failedPayouts',
            'availableBalance',
            'monthlyEarnings',
            'monthlyPayouts',
            'payoutsPaginated',
            'earningsByVehicle',
            'recentPayouts',
            'upcomingBookings',
            'filter',
            'dateRange'
        ));
    }

    /**
     * Get earnings statistics as JSON (for charts).
     */
    public function statistics()
    {
        $user = Auth::user();
        $ownerId = $user->id;

        // Get last 6 months earnings
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $earnings = Booking::whereHas('advertisement', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->where('status', 'completed')
                ->whereYear('trip_completed_at', $date->year)
                ->whereMonth('trip_completed_at', $date->month)
                ->sum('owner_earnings');
            
            $monthlyStats[] = [
                'month' => $month,
                'earnings' => $earnings
            ];
        }

        // Get payout statistics
        $payoutStats = [
            'total' => Payout::where('user_id', $ownerId)->where('status', 'completed')->sum('amount'),
            'pending' => Payout::where('user_id', $ownerId)->where('status', 'pending')->sum('amount'),
            'failed' => Payout::where('user_id', $ownerId)->where('status', 'failed')->sum('amount'),
            'count' => Payout::where('user_id', $ownerId)->where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'monthly' => $monthlyStats,
                'payouts' => $payoutStats,
                'total_earnings' => Booking::whereHas('advertisement', function($q) use ($ownerId) {
                    $q->where('owner_id', $ownerId);
                })->where('status', 'completed')->sum('owner_earnings'),
            ]
        ]);
    }

    /**
     * Get payout details for a specific payout.
     */
    public function showPayout(Payout $payout)
    {
        if ($payout->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access.');
        }

        return view('vehicle-owner.earnings.payout-details', compact('payout'));
    }
}