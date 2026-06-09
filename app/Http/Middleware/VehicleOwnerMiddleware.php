<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleOwnerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->user_type === 'vehicle_owner') {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', 'Access denied. Vehicle owners only.');
    }
}