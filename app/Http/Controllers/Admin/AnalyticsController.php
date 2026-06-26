<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BikeRental;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ============================================================
        // 1. STATISTICS
        // ============================================================
        $stats = [
            'total_users' => User::count(),
            'total_vehicles' => \App\Models\Vehicle::where('is_approved', true)->count(),
            'total_bookings' => Booking::count(),
            'total_rentals' => BikeRental::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'pending_rentals' => BikeRental::where('status', 'pending')->count(),
            'active_rentals' => BikeRental::where('status', 'active')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
        ];

        // ============================================================
        // 2. MONTHLY REVENUE
        // ============================================================
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $revenue = Payment::where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');
            $monthlyRevenue[$month] = $revenue;
        }

        // ============================================================
        // 3. BOOKING TYPES
        // ============================================================
        $bookingTypes = Booking::select('booking_type', DB::raw('count(*) as count'))
            ->groupBy('booking_type')
            ->pluck('count', 'booking_type')
            ->toArray();

        // ============================================================
        // 4. DAILY TRENDS (Last 30 Days)
        // ============================================================
        $dailyTrends = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $day = $date->format('d M');
            $bookings = Booking::whereDate('created_at', $date)->count();
            $rentals = BikeRental::whereDate('created_at', $date)->count();
            $dailyTrends[] = [
                'date' => $day,
                'bookings' => $bookings,
                'rentals' => $rentals,
            ];
        }

        // ============================================================
        // 5. USER GROWTH
        // ============================================================
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M');
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', '<=', $date->month)
                ->count();
            $userGrowth[$month] = $count;
        }

        // ============================================================
        // 6. POPULAR ROUTES
        // ============================================================
        $popularRoutes = Booking::select(
            'pickup_point',
            'dropoff_point',
            DB::raw('count(*) as count')
        )
            ->where('status', 'completed')
            ->groupBy('pickup_point', 'dropoff_point')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        // ============================================================
        // 7. REVENUE BREAKDOWN
        // ============================================================
        $revenueBreakdown = [
            'ride_bookings' => Payment::whereHas('booking')
                ->where('status', 'completed')
                ->sum('amount'),
            'subscriptions' => Payment::whereHas('booking', function($q) {
                $q->where('booking_type', 'subscription');
            })->where('status', 'completed')->sum('amount'),
            'bike_rentals' => Payment::whereNotNull('bike_rental_id')
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        // ============================================================
        // 8. REMOVED: vehicleUtilization (brand column doesn't exist)
        // ============================================================

        return view('admin.analytics.index', compact(
            'stats',
            'monthlyRevenue',
            'bookingTypes',
            'dailyTrends',
            'userGrowth',
            'popularRoutes',
            'revenueBreakdown'
        ));
    }

    public function exportRevenue(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $revenueData = Payment::where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->with(['booking', 'user'])
            ->get();

        $csv = "Date,Transaction ID,Amount,Type,User\n";
        foreach ($revenueData as $payment) {
            $type = $payment->booking_id ? 'Ride Booking' : 'Bike Rental';
            $csv .= sprintf(
                "%s,%s,MWK %s,%s,%s\n",
                $payment->payment_date?->format('d M Y') ?? 'N/A',
                $payment->transaction_id ?? 'N/A',
                number_format($payment->amount ?? 0, 2),
                $type,
                $payment->user->name ?? 'N/A'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="revenue-report.csv"');
    }
}