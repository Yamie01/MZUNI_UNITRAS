<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vehicle Owner Panel - Mzuni UNITRAS')</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #28a745;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* Owner Sidebar - Green theme */
        .owner-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3c2c 0%, #2d5a3a 100%);
            color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h3 {
            margin: 10px 0 0;
            font-size: 1.2rem;
            color: white;
        }
        
        .sidebar-header p {
            color: #8a9e8d;
            font-size: 0.85rem;
            margin-bottom: 0;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section {
            padding: 0 15px;
            margin-bottom: 20px;
        }
        
        .menu-title {
            text-transform: uppercase;
            font-size: 0.7rem;
            color: #8a9e8d;
            letter-spacing: 1px;
            margin-bottom: 10px;
            padding-left: 10px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #e0e0e0;
            text-decoration: none;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s;
        }
        
        .menu-item i {
            width: 25px;
            font-size: 1.1rem;
            margin-right: 10px;
        }
        
        .menu-item:hover, .menu-item.active {
            background: var(--primary-color);
            color: white;
            transform: translateX(5px);
        }
        
        /* Main Content */
        .owner-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Owner Header */
        .owner-header {
            height: var(--header-height);
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .balance-card {
            background: linear-gradient(135deg, var(--primary-color), #1e7e34);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: bold;
        }
        
        .owner-content {
            padding: 30px;
        }
        
        /* Stats Cards - Green theme */
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), #1e7e34);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
        
        .stat-icon i {
            font-size: 1.8rem;
            color: white;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }
        
        /* Tables */
        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .btn-create {
            background: linear-gradient(135deg, var(--primary-color), #1e7e34);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }
        
        /* Forms */
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Copy the rest of the styles from admin layout, but with green theme */
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-rejected {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Owner Sidebar -->
    <div class="owner-sidebar" id="ownerSidebar">
        <div class="sidebar-header">
            <div class="text-center">
                <i class="fas fa-car fa-3x" style="color: #28a745;"></i>
                <h3>Mzuni UNITRAS</h3>
                <p>Vehicle Owner Panel</p>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-title">Dashboard</div>
                <a href="{{ route('vehicle-owner.dashboard') }}" class="menu-item {{ request()->routeIs('vehicle-owner.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Overview</span>
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">My Business</div>
                <a href="{{ route('vehicle-owner.vehicles.index') }}" class="menu-item {{ request()->routeIs('vehicle-owner.vehicles.*') ? 'active' : '' }}">
                    <i class="fas fa-truck"></i>
                    <span>My Vehicles</span>
                </a>
                <a href="{{ route('vehicle-owner.advertisements.index') }}" class="menu-item {{ request()->routeIs('vehicle-owner.advertisements.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i>
                    <span>Advertisements</span>
                </a>
                <a href="{{ route('vehicle-owner.bookings.index') }}" class="menu-item {{ request()->routeIs('vehicle-owner.bookings.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Bookings</span>
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-title">Financial</div>
                <a href="#" class="menu-item">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-history"></i>
                    <span>Transaction History</span>
                </a>
            </div>
            
            <div class="menu-section">
                <a href="{{ route('logout') }}" class="menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="owner-main">
        <!-- Owner Header -->
        <header class="owner-header">
            <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            
            <div class="balance-card">
                <i class="fas fa-wallet me-2"></i>
                Balance: MWK 0.00
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <div class="notification-badge">
                    <i class="fas fa-bell fa-lg text-muted"></i>
                </div>
                
                <div class="user-dropdown" data-bs-toggle="dropdown">
                    <div class="user-avatar" style="background: linear-gradient(135deg, var(--primary-color), #1e7e34);">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="d-none d-md-block">
                        <div class="fw-bold">{{ Auth::user()->name }}</div>
                        <small class="text-muted">Vehicle Owner</small>
                    </div>
                </div>
                
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-cog me-2"></i>Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <main class="owner-content">
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
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function toggleSidebar() {
            document.getElementById('ownerSidebar').classList.toggle('show');
        }
        
        function confirmDelete(event) {
            event.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    event.target.closest('form').submit();
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>