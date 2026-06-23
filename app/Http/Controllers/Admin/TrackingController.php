<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BikeRental;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Show live bike tracking page for admin.
     */
    public function bikes()
    {
        $activeRentals = BikeRental::with(['bike', 'user', 'bike.latestLocation'])
            ->whereIn('status', ['active', 'rented'])
            ->get();

        return view('admin.live-tracking.bikes', compact('activeRentals'));
    }
}