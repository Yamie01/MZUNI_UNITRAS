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
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --sidebar-width: 270px;
            --header-height: 70px;
            --primary-color: #0D6EFD;
            --sidebar-bg: #0f1729;
            --sidebar-hover: #1a2744;
            --text-muted: #94a3b8;
        }
        
        * { box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        /* ============================================================
           SIDEBAR
           ============================================================ */
        .owner-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: #e2e8f0;
            overflow-y: auto;
            z-index: 1050;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .owner-sidebar::-webkit-scrollbar { width: 4px; }
        .owner-sidebar::-webkit-scrollbar-thumb { background: #2d3748; border-radius: 4px; }
        
        /* Sidebar Header */
        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            text-align: center;
        }
        .sidebar-header .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #0D6EFD, #0a58ca);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 1.8rem;
            color: white;
        }
        .sidebar-header h5 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }
        .sidebar-header p {
            color: var(--text-muted);
            font-size: 0.75rem;
            margin: 2px 0 0;
            letter-spacing: 0.5px;
        }
        
        /* Sidebar Menu */
        .sidebar-menu {
            padding: 1rem 0.75rem 1.5rem;
            flex: 1;
        }
        .menu-section { margin-bottom: 1.5rem; }
        .menu-title {
            font-size: 0.6rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 0 0.75rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.6rem 0.75rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s;
            font-size: 0.85rem;
            font-weight: 500;
            gap: 10px;
        }
        .menu-item i {
            width: 20px;
            font-size: 1rem;
            text-align: center;
            flex-shrink: 0;
        }
        .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        .menu-item.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        .menu-item.logout {
            margin-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 1rem;
            color: #ef4444;
        }
        .menu-item.logout:hover { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        
        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        .owner-main {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            background: #f1f5f9;
        }
        
        /* Header */
        .owner-header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 1040;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }
        .owner-header .toggle-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: #1e293b;
        }
        
        /* Balance Badge */
        .balance-badge {
            background: linear-gradient(135deg, #0D6EFD, #0a58ca);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }
        .balance-badge i { font-size: 1rem; }
        
        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 0.3rem 0.8rem 0.3rem 0.3rem;
            border-radius: 50px;
            transition: background 0.2s;
        }
        .user-dropdown:hover { background: #f1f5f9; }
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0D6EFD, #0a58ca);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        .user-name {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1e293b;
            line-height: 1.2;
        }
        .user-role {
            font-size: 0.65rem;
            color: #94a3b8;
        }
        
        /* Content Area */
        .owner-content {
            padding: 1.5rem 2rem 2rem;
        }
        
        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (max-width: 992px) {
            .owner-header .toggle-btn { display: block; }
            .owner-sidebar {
                transform: translateX(-100%);
            }
            .owner-sidebar.open {
                transform: translateX(0);
            }
            .owner-main {
                margin-left: 0;
            }
            .owner-content { padding: 1rem; }
            .balance-badge { font-size: 0.75rem; padding: 0.3rem 0.8rem; }
            .user-name { display: none; }
            .user-role { display: none; }
        }
        
        @media (max-width: 576px) {
            .owner-header { padding: 0 1rem; }
            .balance-badge span { display: none; }
        }
        
        /* ============================================================
           OVERLAY FOR MOBILE
           ============================================================ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1045;
        }
        .sidebar-overlay.active { display: block; }
    </style>
    
    @stack('styles')
</head>
<body>

    <!-- ============================================================
    SIDEBAR OVERLAY (Mobile)
    ============================================================ -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <!-- ============================================================
    SIDEBAR
    ============================================================ -->
    <aside class="owner-sidebar" id="ownerSidebar">
        <div class="sidebar-header">
            <div class="brand-icon">
                <i class="fas fa-car"></i>
            </div>
            <h5>Mzuni UNITRAS</h5>
            <p>Vehicle Owner Panel</p>
        </div>
        
        <nav class="sidebar-menu">
            <!-- Dashboard -->
            <div class="menu-section">
                <div class="menu-title">Dashboard</div>
                <a href="{{ route('vehicle-owner.dashboard') }}" class="menu-item {{ request()->routeIs('vehicle-owner.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Overview</span>
                </a>
            </div>
            
            <!-- My Business -->
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
            
            <!-- Financial -->
            <div class="menu-section">
                <div class="menu-title">Financial</div>
                <a href="{{ route('vehicle-owner.earnings') }}" class="menu-item {{ request()->routeIs('vehicle-owner.earnings') ? 'active' : '' }}">
                    <i class="fas fa-wallet"></i>
                    <span>Earnings</span>
                </a>
                <a href="#" class="menu-item">
                    <i class="fas fa-history"></i>
                    <span>Transaction History</span>
                </a>
            </div>
            
            <!-- Logout -->
            <div class="menu-section">
                <a href="#" class="menu-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>
    </aside>

    <!-- ============================================================
    MAIN CONTENT
    ============================================================ -->
    <div class="owner-main">
        <!-- Header -->
        <header class="owner-header">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="balance-badge">
                <i class="fas fa-wallet"></i>
                <span>Balance: MWK 0.00</span>
            </div>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Notifications -->
                <div class="position-relative">
                    <i class="fas fa-bell fa-lg text-secondary" style="cursor:pointer;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.5rem;">
                        0
                    </span>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div>
                            <div class="user-name">{{ Auth::user()->name }}</div>
                            <div class="user-role">Vehicle Owner</div>
                        </div>
                        <i class="fas fa-chevron-down text-secondary" style="font-size:0.6rem; margin-left:4px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content -->
        <main class="owner-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
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
    <script>
        function toggleSidebar() {
            document.getElementById('ownerSidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('active');
        }
        
        function closeSidebar() {
            document.getElementById('ownerSidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
        
        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                closeSidebar();
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>