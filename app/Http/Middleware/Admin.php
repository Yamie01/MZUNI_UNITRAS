<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->user_type !== 'admin') {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}