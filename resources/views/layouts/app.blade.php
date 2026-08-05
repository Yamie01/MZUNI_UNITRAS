<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Mzuni UNITRAS')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --mzuni-green: #00693E;
            --mzuni-green-dark: #004d2e;
            --mzuni-green-light: #e8f5e9;
            --mzuni-green-gradient: linear-gradient(135deg, #00693E, #00843D);
            --mzuni-gold: #FFB300;
            --mzuni-white: #FFFFFF;
            --mzuni-dark: #1A1A1A;
            --mzuni-gray: #F5F7F5;
            --mzuni-text: #333333;
            --sidebar-width: 260px;
            --navbar-height: 64px;
        }

        * {
            font-family: 'Figtree', sans-serif;
            box-sizing: border-box;
        }

        body {
            background: var(--mzuni-gray);
            color: var(--mzuni-text);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        /* ===== LOGIN PAGE ===== */
.login-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #00693E 0%, #004d2e 50%, #003d23 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.login-page::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 600px;
    height: 600px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 50%;
}

.login-page::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 215, 0, 0.04);
    border-radius: 50%;
}

.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    z-index: 1;
}

.login-card .text-mzuni-green {
    color: #00693E;
}

.login-card .form-control {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 0.7rem 1rem;
    transition: all 0.3s ease;
}

.login-card .form-control:focus {
    border-color: #00693E;
    box-shadow: 0 0 0 4px rgba(0, 105, 62, 0.12);
}

.login-card .btn-primary {
    background: #00693E;
    border-color: #00693E;
    border-radius: 12px;
    padding: 0.8rem;
    transition: all 0.3s ease;
}

.login-card .btn-primary:hover {
    background: #004d2e;
    border-color: #004d2e;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 105, 62, 0.3);
}

.login-card a.text-mzuni-green {
    color: #00693E;
    text-decoration: none;
    font-weight: 600;
}

.login-card a.text-mzuni-green:hover {
    color: #004d2e;
    text-decoration: underline;
}

.login-card .form-check-input:checked {
    background-color: #00693E;
    border-color: #00693E;
}

@media (max-width: 576px) {
    .login-card {
        padding: 1.5rem;
        margin: 1rem;
        border-radius: 16px;
    }

    .login-card h4 {
        font-size: 1.3rem;
    }
}
        /* ===== NAVBAR ===== */
        .navbar-mzuni {
            background: var(--mzuni-green) !important;
            box-shadow: 0 2px 15px rgba(0, 105, 62, 0.25);
            padding: 0.7rem 0;
            height: var(--navbar-height);
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .navbar-mzuni .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-mzuni .navbar-brand img {
            height: 36px;
            width: auto;
        }

        .navbar-mzuni .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s;
        }

        .navbar-mzuni .nav-link:hover {
            color: white !important;
            opacity: 0.8;
        }

        .navbar-mzuni .btn-outline-light:hover {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }

        .navbar-mzuni .btn-gold {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
            font-weight: 600;
        }

        .navbar-mzuni .btn-gold:hover {
            background: #e6a200;
            border-color: #e6a200;
        }

        /* ===== DASHBOARD LAYOUT ===== */
        .dashboard-wrapper {
            display: flex;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* ===== SIDEBAR ===== */
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background: white;
            border-right: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
            position: sticky;
            top: var(--navbar-height);
            height: calc(100vh - var(--navbar-height));
            overflow-y: auto;
            flex-shrink: 0;
        }

        .dashboard-sidebar .user-avatar {
            background: var(--mzuni-green-gradient);
            color: white;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            margin-bottom: 2px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.5rem;
            color: var(--mzuni-text);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .sidebar-menu a i {
            width: 22px;
            margin-right: 12px;
            font-size: 1rem;
            text-align: center;
        }

        .sidebar-menu a:hover {
            background: var(--mzuni-green-light);
            color: var(--mzuni-green);
        }

        .sidebar-menu a.active {
            background: var(--mzuni-green-light);
            color: var(--mzuni-green);
            border-left-color: var(--mzuni-green);
        }

        .sidebar-menu .menu-divider {
            height: 1px;
            background: rgba(0, 0, 0, 0.06);
            margin: 0.75rem 1.5rem;
        }

        /* ===== MAIN CONTENT ===== */
        .dashboard-content {
            flex: 1;
            padding: 1.5rem 2rem;
            background: var(--mzuni-gray);
            min-height: calc(100vh - var(--navbar-height));
        }

        /* ===== STATS CARDS ===== */
        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 105, 62, 0.10);
            border-color: var(--mzuni-green);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            background: var(--mzuni-green-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--mzuni-dark);
        }

        .stat-label {
            color: #888;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--mzuni-green-dark);
            border-color: var(--mzuni-green-dark);
        }

        .btn-success {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
            font-weight: 600;
        }

        .btn-success:hover {
            background: var(--mzuni-green-dark);
            border-color: var(--mzuni-green-dark);
        }

        .btn-outline-primary {
            color: var(--mzuni-green);
            border-color: var(--mzuni-green);
        }

        .btn-outline-primary:hover {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
            color: white;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 105, 62, 0.08);
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .card-header.bg-primary {
            background: var(--mzuni-green-gradient) !important;
            color: white;
        }

        .card-body {
            padding: 1.25rem;
        }

        /* ===== RIDE & BIKE CARDS ===== */
        .ride-card, .bike-card {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .ride-card:hover, .bike-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0, 105, 62, 0.10);
            border-color: var(--mzuni-green);
        }

        .card-img-top {
            height: 160px;
            background: var(--mzuni-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .card-img-top img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-img-top i {
            font-size: 3.5rem;
            color: var(--mzuni-green);
            opacity: 0.5;
        }

        /* ===== BADGES ===== */
        .badge-success {
            background: var(--mzuni-green);
            color: white;
        }

        .badge-warning {
            background: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .dashboard-sidebar {
                position: fixed;
                top: var(--navbar-height);
                left: -100%;
                width: 280px;
                height: calc(100vh - var(--navbar-height));
                transition: left 0.3s ease;
                z-index: 1040;
                background: white;
                box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
            }

            .dashboard-sidebar.show {
                left: 0;
            }

            .dashboard-content {
                padding: 1rem;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                top: var(--navbar-height);
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.3);
                z-index: 1039;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        @media (max-width: 576px) {
            .navbar-mzuni .navbar-brand {
                font-size: 1rem;
            }

            .navbar-mzuni .navbar-brand img {
                height: 30px;
            }

            .stat-number {
                font-size: 1.3rem;
            }

            .dashboard-content {
                padding: 0.75rem;
            }
        }

        /* ===== UTILITIES ===== */
        .text-mzuni-green {
            color: var(--mzuni-green);
        }

        .bg-mzuni-green {
            background: var(--mzuni-green);
        }

        .bg-mzuni-green-light {
            background: var(--mzuni-green-light);
        }

        .border-mzuni {
            border-color: var(--mzuni-green) !important;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.4s ease forwards;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: var(--mzuni-gray);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--mzuni-green);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--mzuni-green-dark);
        }

        /* ===== SELECTION ===== */
        ::selection {
            background: var(--mzuni-green);
            color: white;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg navbar-mzuni">
        <div class="container-fluid">
            <div class="d-flex align-items-center gap-3">
                <!-- Mobile Toggle -->
                <button class="btn btn-link d-lg-none text-white p-0" onclick="toggleSidebar()" style="font-size: 1.3rem;">
                    <i class="fas fa-bars"></i>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni">
                    <span>Mzuni UNITRAS</span>
                </a>
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @auth
                        <li class="nav-item">
                            <span class="text-white-50 d-none d-lg-inline me-2">|</span>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle fa-lg"></i>
                                <span>{{ Str::limit(Auth::user()->name, 20) }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i> My Bookings
                                </a></li>
                                @if(Auth::user()->isVehicleOwner())
                                    <li><a class="dropdown-item" href="{{ route('vehicle-owner.dashboard') }}">
                                        <i class="fas fa-car me-2"></i> Vehicle Owner
                                    </a></li>
                                    <li><a class="dropdown-item" href="{{ route('payout.dashboard') }}">
                                        <i class="fas fa-wallet me-2"></i> Payouts
                                    </a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-gold btn-sm" href="{{ route('register') }}">Sign Up</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <!-- ===== CONTENT ===== -->
    <main>
        @yield('content')
    </main>

    <!-- ===== SCRIPTS ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle for Mobile
        function toggleSidebar() {
            const sidebar = document.getElementById('dashboardSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) {
                sidebar.classList.toggle('show');
                if (overlay) {
                    overlay.classList.toggle('show');
                }
            }
        }

        // Close sidebar when clicking overlay
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    const sidebar = document.getElementById('dashboardSidebar');
                    if (sidebar) {
                        sidebar.classList.remove('show');
                        overlay.classList.remove('show');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>