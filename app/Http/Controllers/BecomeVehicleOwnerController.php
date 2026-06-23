<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BecomeVehicleOwnerController extends Controller
{
    public function create()
    {
        return view('auth.become-vehicle-owner');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->user_type === 'vehicle_owner') {
            return redirect()->route('offer.ride')->with('info', 'You are already a vehicle owner.');
        }

        $user->update(['user_type' => 'vehicle_owner']);

        return redirect()->route('vehicle-owner.vehicles.create')
            ->with('success', 'You are now a Vehicle Owner! Please add your first vehicle for approval.');
    }
}