<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:student,staff,vehicle_owner'],
            'phone' => ['required', 'string', 'max:15'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'status' => 'active',
        ]);

        // Fire registered event (optional, for email verification)
        event(new Registered($user));

        // Log the user in
        Auth::login($user);

        // Redirect based on user type
        return $this->redirectBasedOnUserType($user);
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