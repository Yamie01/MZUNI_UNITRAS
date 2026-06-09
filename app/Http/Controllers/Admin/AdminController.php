<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard()
    {
        return view('admin.dashboard'); // load the admin dashboard view
    }
}
