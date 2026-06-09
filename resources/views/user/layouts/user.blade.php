<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account - Mzuni UNITRAS')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #00529b;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* User Navigation */
        .user-nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .nav-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .nav-link {
            color: #333;
            font-weight: 500;
            padding: 8px 15px !important;
            border-radius: 20px;
            transition: all 0.3s;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--primary-color);
            color: white !important;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 15px;
            border-radius: 25px;
            transition: all 0.3s;
        }
        
        .user-profile:hover {
            background: #f0f0f0;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), #003f75);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        /* User Content */
        .user-content {
            padding: 40px 0;
            min-height: calc(100vh - 150px);
        }
        
        /* Dashboard Cards */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.05);
            text-align: center;
            cursor: pointer;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), #003f75);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .card-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .card-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .card-text {
            color: #666;
            margin-bottom: 20px;
        }
        
        /* Footer */
        .user-footer {
            background: #1a1a1a;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        
        /* Tables */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .booking-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- User Navigation -->
    <nav class="user-nav">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('home') }}" class="nav-brand">
                    <i class="fas fa-bus me-2"></i>Mzuni UNITRAS
                </a>
                
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('search') }}" class="nav-link">
                        <i class="fas fa-search me-1"></i>Find Rides
                    </a>
                    
                    <div class="dropdown">
                        <div class="user-profile" data-bs-toggle="dropdown">
                            <div class="avatar">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="d-none d-md-block">
                                <div class="fw-bold">{{ Auth::user()->name }}</div>
                                <small class="text-muted">{{ ucfirst(Auth::user()->user_type) }}</small>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                            <a class="dropdown-item" href="{{ route('user.bookings.index') }}">
                                <i class="fas fa-calendar-check me-2"></i>My Bookings
                            </a>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="user-content">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="user-footer">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Mzuni UNITRAS. All rights reserved.</p>
        </div>
    </footer>
    
    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>