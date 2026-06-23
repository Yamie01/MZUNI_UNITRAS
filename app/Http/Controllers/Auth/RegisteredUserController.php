<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
 * Handle an incoming registration request.
 */
public function store(Request $request)
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'phone' => ['nullable', 'string', 'max:20'],
        'user_type' => ['required', 'in:student,staff,vehicle_owner'],
        'university_id' => ['nullable', 'string', 'max:50', 'unique:users,university_id'],
        'department' => ['nullable', 'string', 'max:100'],
        'redirect_to' => ['nullable', 'string'], // ✅ accept redirect_to
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'phone' => $request->phone,
        'user_type' => $request->user_type,
        'university_id' => $request->university_id,
        'department' => $request->department,
        'status' => 'active',
    ]);

    event(new Registered($user));

    Auth::login($user);

    // ✅ Redirect to intended page – check redirect_to first, then session, then fallback
    $redirectTo = $request->input('redirect_to') 
        ?? session('url.intended') 
        ?? route('dashboard');

    // Clear the session intent so it doesn't persist
    session()->forget('url.intended');

    return redirect($redirectTo);
}
    /**
     * Redirect users based on their type.
     */
    protected function redirectToDashboard($user)
    {
        if ($user->user_type === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->user_type === 'vehicle_owner') {
            return redirect()->route('vehicle-owner.dashboard');
        }
        return redirect()->route('dashboard');
    }
}