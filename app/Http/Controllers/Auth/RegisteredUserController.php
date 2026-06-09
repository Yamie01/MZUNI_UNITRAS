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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:student,staff,vehicle_owner'],
            'phone' => ['required', 'string', 'max:15'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        // Handle additional fields for student/staff
        if (in_array($request->user_type, ['student', 'staff'])) {
            $user->update([
                'university_id' => $request->university_id,
                'department' => $request->department,
            ]);
        }

        // Handle additional fields for vehicle owner
        if ($request->user_type === 'vehicle_owner') {
            $user->update([
                'driving_license' => $request->driving_license,
                'license_expiry' => $request->license_expiry,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // ✅ Check for redirect_to parameter (for booking/rental after registration)
        $redirectTo = $request->input('redirect_to');
        
        if ($redirectTo) {
            return redirect($redirectTo);
        }

        // Fallback: Redirect based on user type
        return $this->redirectToDashboard($user);
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