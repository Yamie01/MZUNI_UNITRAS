<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * READ: Display all users with filtering
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by user type
        if ($request->filled('type')) {
            $query->where('user_type', $request->type);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search by name or email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        // Get counts for dashboard
        $counts = [
            'total' => User::count(),
            'students' => User::where('user_type', 'student')->count(),
            'staff' => User::where('user_type', 'staff')->count(),
            'owners' => User::where('user_type', 'vehicle_owner')->count(),
            'admins' => User::where('user_type', 'admin')->count(),
            'active' => User::where('status', 'active')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'counts'));
    }

    /**
     * SHOW: Display single user details
     */
    public function show(User $user)
    {
        $user->load(['vehicles', 'advertisements', 'bookings']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * EDIT: Show edit form
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * UPDATE: Update user information
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:student,staff,vehicle_owner,admin',
            'status' => 'required|in:active,inactive,suspended',
            'university_id' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only([
            'name', 'email', 'phone', 'user_type', 'status',
            'university_id', 'department'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * DELETE: Remove user
     */
    public function destroy(User $user)
    {
        // Check if user has related records
        if ($user->vehicles()->count() > 0) {
            return back()->with('error', 'Cannot delete user with registered vehicles.');
        }
        
        if ($user->bookings()->count() > 0) {
            return back()->with('error', 'Cannot delete user with booking history.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * CUSTOM: Suspend user
     */
    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        
        return back()->with('success', 'User suspended successfully.');
    }

    /**
     * CUSTOM: Activate user
     */
    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        
        return back()->with('success', 'User activated successfully.');
    }

    /**
     * CUSTOM: Export users to CSV
     */
    public function export()
    {
        $users = User::all();
        
        $filename = 'users_' . date('Y-m-d') . '.csv';
        $handle = fopen($filename, 'w');
        
        // Add headers
        fputcsv($handle, ['ID', 'Name', 'Email', 'Type', 'Status', 'Joined']);
        
        // Add data
        foreach ($users as $user) {
            fputcsv($handle, [
                $user->id,
                $user->name,
                $user->email,
                $user->user_type,
                $user->status,
                $user->created_at->format('Y-m-d')
            ]);
        }
        
        fclose($handle);
        
        return response()->download($filename)->deleteFileAfterSend();
    }
}