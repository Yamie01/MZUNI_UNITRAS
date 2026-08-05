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
<<<<<<< HEAD
     */
    public function showLoginForm()
    {
=======
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showLoginForm(Request $request)
    {
        // Store the redirect URL in session before showing login
        if ($request->has('redirect_to')) {
            session(['url.intended' => $request->redirect_to]);
        }

>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        return view('auth.login');
    }

    /**
     * Handle a login request.
<<<<<<< HEAD
=======
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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

<<<<<<< HEAD
            // ✅ Get redirect_to from session or request
            $redirectTo = session('url.intended') ?? $request->input('redirect_to');

            if ($redirectTo && $redirectTo !== '') {
                session()->forget('url.intended');
                return redirect($redirectTo);
=======
            // Check for redirect URL from session or request
            $redirectTo = session('url.intended') ?? $request->input('redirect_to');

            if ($redirectTo) {
                session()->forget('url.intended');

                // If user is vehicle owner and redirect is offer-ride, send to publish page
                if ($user->isVehicleOwner() && str_contains($redirectTo, 'offer-ride')) {
                    return redirect()->route('vehicle-owner.advertisements.create')
                        ->with('info', 'Publish your ride here.');
                }

                return redirect()->to($redirectTo);
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
=======
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
<<<<<<< HEAD
     * Redirect users based on their type.
=======
     * Redirect users based on their user type.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    protected function redirectBasedOnUserType($user)
    {
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        }
<<<<<<< HEAD
        if ($user->user_type === 'vehicle_owner') {
            return redirect()->route('vehicle-owner.dashboard');
        }
=======

        if ($user->user_type === 'vehicle_owner') {
            return redirect()->route('vehicle-owner.dashboard');
        }

>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        return redirect()->route('dashboard');
    }
}