<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Account status check
        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is not active. Please contact the administrator.']);
        }

        // ✅ Get redirect_to from the form submission (NOT from query string)
        $redirectTo = $request->input('redirect_to');
        
        Log::info('Login - redirect_to value', ['redirect_to' => $redirectTo]);

        // ✅ If redirect_to exists, go there immediately
        if ($redirectTo && $redirectTo !== '') {
            return redirect($redirectTo);
        }

        // Fallback to role-based redirect
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        if ($user->user_type === 'vehicle_owner') {
            return redirect()->route('vehicle-owner.dashboard');
        }
        return redirect()->route('dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}