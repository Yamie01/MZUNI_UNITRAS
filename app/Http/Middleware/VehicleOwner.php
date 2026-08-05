<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VehicleOwner
{
    public function handle(Request $request, Closure $next)
    {
<<<<<<< HEAD
        if (!auth()->check() || auth()->user()->user_type !== 'vehicle_owner') {
            abort(403, 'Unauthorized access.');
        }

=======
        $user = auth()->user();
        
        if (!$user || !$user->isVehicleOwner()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Unauthorized. Vehicle owner access required.'
                ], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. Vehicle owner privileges required.');
        }
        
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        return $next($request);
    }
}