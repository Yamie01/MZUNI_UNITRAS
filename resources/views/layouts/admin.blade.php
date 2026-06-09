<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - Mzuni UNITRAS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @stack('styles')
    
    <style>
        :root {
            --primary: #00529b;
            --primary-dark: #003f75;
            --primary-light: #e8f0fe;
            --secondary: #ff6b35;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --dark: #1a1a2e;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
            --sidebar-width: 280px;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background-color: #f0f2f5;
            overflow-x: hidden;
        }
        
        /* Top Navbar */
        .navbar-top {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 0.8rem 1.5rem;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 1000;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1001;
            transition: all 0.3s;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-header h4 {
            font-size: 1.2rem;
            margin-top: 0.5rem;
            margin-bottom: 0;
        }
        
        .sidebar-header p {
            font-size: 0.7rem;
            opacity: 0.7;
            margin-bottom: 0;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .sidebar-nav .nav-item {
            list-style: none;
            margin-bottom: 0.25rem;
        }
        
        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
            gap: 12px;
        }
        
        .sidebar-nav .nav-link i {
            width: 24px;
            font-size: 1.1rem;
        }
        
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-right: 3px solid var(--secondary);
        }
        
        .sidebar-nav .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 1rem 0;
        }
        
        .sidebar-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.75rem;
            text-align: center;
            background: rgba(0,0,0,0.2);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: 70px;
            min-height: 100vh;
        }
        
        .content-wrapper {
            padding: 1.5rem;
        }
        
        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.2s;
            border: 1px solid #e9ecef;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .navbar-top {
                left: 0;
            }
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            @if(file_exists(public_path('images/mzuni-logo.png')))
                <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni UNITRAS" height="50">
            @else
                <i class="fas fa-university fa-2x"></i>
            @endif
            <h4>Mzuni UNITRAS</h4>
            <p>Administrator Panel</p>
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.vehicles.*') ? 'active' : '' }}" href="{{ route('admin.vehicles.index') }}">
                    <i class="fas fa-car"></i>
                    <span>Vehicles</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}" href="{{ route('admin.advertisements.index') }}">
                    <i class="fas fa-ad"></i>
                    <span>Advertisements</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bikes.*') ? 'active' : '' }}" href="{{ route('admin.bikes.index') }}">
                    <i class="fas fa-bicycle"></i>
                    <span>Bikes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.bike-rentals.*') ? 'active' : '' }}" href="{{ route('admin.bike-rentals.index') }}">
                    <i class="fas fa-history"></i>
                    <span>Bike Rentals</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.live-tracking.*') ? 'active' : '' }}" href="{{ route('admin.live-tracking.bikes') }}">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Live Tracking</span>
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Site</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
        
    </div>
    
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    <div class="sidebar-footer">
            <small>&copy; {{ date('Y') }} Mzuzu University</small>
        </div>
    <!-- Top Navbar -->
    <nav class="navbar-top">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end">
                    <small class="text-muted d-block">System Administrator</small>
                    <strong>{{ Auth::user()->name }}</strong>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle fa-2x"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth < 768 && sidebar && sidebar.classList.contains('show')) {
                if (!sidebar.contains(event.target) && !toggle?.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>