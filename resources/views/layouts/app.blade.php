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

    <!-- Custom Styles -->
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
        }

        * {
            font-family: 'Figtree', sans-serif;
        }

        body {
            background: var(--mzuni-gray);
            color: var(--mzuni-text);
        }

        /* Navbar */
        .navbar-mzuni {
            background: var(--mzuni-green) !important;
            box-shadow: 0 2px 15px rgba(0, 105, 62, 0.3);
            padding: 0.8rem 0;
        }
        .navbar-mzuni .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.3rem;
        }
        .navbar-mzuni .navbar-brand img {
            height: 40px;
        }
        .navbar-mzuni .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
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

        /* Buttons */
        .btn-primary {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
        }
        .btn-primary:hover {
            background: var(--mzuni-green-dark);
            border-color: var(--mzuni-green-dark);
        }

        .btn-success {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
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

        .btn-gold {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }
        .btn-gold:hover {
            background: #e6a200;
            border-color: #e6a200;
            color: var(--mzuni-dark);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 105, 62, 0.12);
        }
        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.2rem 1.5rem;
        }
        .card-header.bg-primary {
            background: var(--mzuni-green-gradient) !important;
        }

        /* Dashboard Sidebar */
        .dashboard-sidebar {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            padding: 1.5rem 0;
        }
        .dashboard-sidebar .user-avatar {
            background: var(--mzuni-green-gradient);
            color: white;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 4px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.7rem 1.5rem;
            color: var(--mzuni-text);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-weight: 500;
        }
        .sidebar-menu a i {
            width: 24px;
            margin-right: 12px;
            font-size: 1.1rem;
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

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 105, 62, 0.12);
            border-color: var(--mzuni-green);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            background: var(--mzuni-green-gradient);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--mzuni-dark);
        }
        .stat-label {
            color: #888;
            font-size: 0.85rem;
        }

        /* Ride & Bike Cards */
        .ride-card, .bike-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            height: 100%;
        }
        .ride-card:hover, .bike-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 105, 62, 0.12);
            border-color: var(--mzuni-green);
        }
        .card-img-top {
            height: 160px;
            background: var(--mzuni-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-img-top i {
            font-size: 4rem;
            color: var(--mzuni-green);
        }

        /* Badges */
        .badge-success {
            background: var(--mzuni-green);
            color: white;
        }
        .badge-warning {
            background: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }

        /* Footer */
        .footer-mzuni {
            background: var(--mzuni-green-dark);
            color: rgba(255, 255, 255, 0.8);
            padding: 3rem 0 1.5rem;
            margin-top: 3rem;
        }
        .footer-mzuni a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        .footer-mzuni a:hover {
            color: white;
        }
        .footer-mzuni hr {
            border-color: rgba(255, 255, 255, 0.1);
        }

        /* Welcome Page */
        .welcome-hero {
            background: linear-gradient(135deg, var(--mzuni-green), var(--mzuni-green-dark));
            padding: 4rem 0;
            color: white;
        }
        .welcome-hero .btn-primary {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }
        .welcome-hero .btn-primary:hover {
            background: #e6a200;
            border-color: #e6a200;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.4rem;
            }
            .navbar-mzuni .navbar-brand {
                font-size: 1rem;
            }
            .navbar-mzuni .navbar-brand img {
                height: 30px;
            }
        }

        /* Animations */
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
            animation: fadeInUp 0.5s ease forwards;
        }

        /* Timer */
        .timer-display {
            font-size: 3rem;
            font-weight: 700;
            color: var(--mzuni-green);
            font-family: 'Courier New', monospace;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-mzuni">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni" class="me-2">
                <span>Mzuni UNITRAS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('search') }}">Rides</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i> My Bookings
                                </a></li>
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

    <!-- Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-mzuni">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="fas fa-bus me-2"></i>Mzuni UNITRAS</h5>
                    <p class="small">Mzuzu University Unified Transport System</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('search') }}">Find Rides</a></li>
                        <li><a href="{{ route('user.bookings.index') }}">My Bookings</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contact</h6>
                    <p class="small mb-1"><i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw</p>
                    <p class="small"><i class="fas fa-phone me-2"></i>+265 990 179 811</p>
                </div>
            </div>
            <hr>
            <div class="text-center small">
                &copy; {{ date('Y') }} Mzuni UNITRAS. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>