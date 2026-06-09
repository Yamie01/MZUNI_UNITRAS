<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check account status
            if ($user->status !== 'active') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is not active. Please contact the administrator.',
                ]);
            }

            
            if ($redirectTo && $redirectTo !== '') {
            // Force debug - see what happens
            return redirect($redirectTo);
            }
            // Redirect based on user type
            return $this->redirectBasedOnUserType($user);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Redirect users based on their type.
     */
                protected function redirectBasedOnUserType($user)
{
    if ($user->user_type === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->user_type === 'vehicle_owner') {
        return redirect()->route('vehicle-owner.dashboard');
    }
    return redirect()->route('dashboard');
}

}