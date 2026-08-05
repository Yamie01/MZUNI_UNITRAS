<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mzuni UNITRAS - Unified Transport System</title>

    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS + JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        /* ===== MZUZU UNIVERSITY BRAND COLORS ===== */
        :root {
            --mzuni-green: #00693E;
            --mzuni-green-dark: #004d2e;
            --mzuni-green-light: #e8f5e9;
            --mzuni-gold: #FFB300;
            --mzuni-white: #FFFFFF;
            --mzuni-dark: #1A1A1A;
            --mzuni-gray: #F5F7F5;
            --mzuni-text: #333333;
            --mzuni-gradient: linear-gradient(135deg, #00693E, #00843D);
            font-size: 18px;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            font-size: 1.05rem;
            line-height: 1.7;
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1200') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

<<<<<<< HEAD
        /* Overlay for better readability */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

<<<<<<< HEAD
        /* All content above overlay */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .content-wrapper {
            position: relative;
            z-index: 1;
        }

<<<<<<< HEAD
        /* ===== NAVBAR - TRANSPARENT ===== */
        .navbar {
            background: rgba(0, 0, 0, 0.7) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            padding: 0.9rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
=======
        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(4, 104, 21, 0.75) !important;
            backdrop-filter: blur(12px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.2);
            padding: 0.9rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .navbar-brand img {
            height: 45px;
            width: auto;
            filter: brightness(0) invert(1);
<<<<<<< HEAD
            /* This makes the logo white - good for dark backgrounds */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        }
        .navbar-brand .brand-text {
            color: white;
        }
        .navbar-brand .brand-green {
            color: #00693E;
            background: white;
            padding: 2px 12px;
            border-radius: 8px;
            font-weight: 800;
        }
        .navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: 0.3s;
        }
        .navbar .nav-link:hover {
            color: var(--mzuni-gold) !important;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
            transition: 0.3s;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--mzuni-green-dark);
            border-color: var(--mzuni-green-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 105, 62, 0.3);
        }

        .btn-success {
            background: var(--mzuni-green);
            border-color: var(--mzuni-green);
            font-weight: 600;
        }
        .btn-success:hover {
            background: var(--mzuni-green-dark);
            border-color: var(--mzuni-green-dark);
            transform: translateY(-2px);
        }

        .btn-warning {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
            font-weight: 600;
        }
        .btn-warning:hover {
            background: #e6a200;
            border-color: #e6a200;
            color: var(--mzuni-dark);
            transform: translateY(-2px);
        }

        .btn-outline-light:hover {
            background: var(--mzuni-gold);
            border-color: var(--mzuni-gold);
            color: var(--mzuni-dark);
        }

<<<<<<< HEAD
        /* ===== HERO - TRANSPARENT ===== */
=======
        /* ===== HERO ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .hero {
            padding: 7rem 0 3rem;
            min-height: 600px;
            display: flex;
            align-items: center;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
        }
        .hero-title .highlight {
            color: var(--mzuni-gold);
        }
<<<<<<< HEAD
        .hero-title .brand-green {
            color: #00693E;
            background: white;
            padding: 2px 12px;
            border-radius: 10px;
            display: inline-block;
        }
        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255,255,255,0.95);
        }
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .hero-description {
            color: rgba(255,255,255,0.85);
            font-size: 1.1rem;
        }

<<<<<<< HEAD
        /* Trust Badge */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .trust-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .trust-badge i {
            color: var(--mzuni-gold);
        }

<<<<<<< HEAD
        /* ===== SEARCH CARD - GLASSMORPHISM ===== */
=======
        /* ===== SEARCH CARD ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .search-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            padding: 2rem;
            color: var(--mzuni-text);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .search-card .form-label {
            font-weight: 600;
            color: #555;
            font-size: 0.9rem;
        }
        .search-card .form-select,
        .search-card .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 0.7rem 1rem;
            background: white;
        }
        .search-card .form-select:focus,
        .search-card .form-control:focus {
            border-color: var(--mzuni-green);
            box-shadow: 0 0 0 0.2rem rgba(0, 105, 62, 0.15);
        }

<<<<<<< HEAD
        /* ===== SERVICE TABS - TRANSPARENT ===== */
=======
        /* ===== SERVICE TABS ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .tab-btn {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            color: white;
            padding: 0.7rem 2rem;
            border-radius: 50px;
            transition: 0.3s;
            font-weight: 500;
        }
        .tab-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }
        .tab-btn.active {
            background: white;
            color: var(--mzuni-green);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-color: white;
        }

<<<<<<< HEAD
        /* ===== SECTION HEADERS - GREEN ===== */
=======
        /* ===== SECTION HEADERS ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .section-title {
            color: white;
            font-weight: 800;
            font-size: 2.5rem;
        }
        .section-title .highlight {
            color: var(--mzuni-gold);
        }
        .section-title .green {
            color: #00693E;
            background: white;
            padding: 2px 12px;
            border-radius: 10px;
            display: inline-block;
        }

        .section-subtitle {
            color: rgba(255,255,255,0.85);
            font-size: 1.2rem;
        }

<<<<<<< HEAD
        /* ===== RIDE & BIKE CARDS - GLASSMORPHISM ===== */
        .ride-card, .bike-card {
            background: rgba(255, 255, 255, 0.92);
=======
        /* ===== RIDE & BIKE CARDS ===== */
        .ride-card, .bike-card {
            background: rgba(255, 255, 255, 0.95);
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
            backdrop-filter: blur(20px);
            color: var(--mzuni-text);
            border-radius: 20px;
            transition: 0.3s ease;
            overflow: hidden;
            height: 100%;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .ride-card:hover, .bike-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            border-color: var(--mzuni-green);
        }
        .price {
            color: var(--mzuni-green);
            font-weight: 800;
            font-size: 1.1rem;
        }
<<<<<<< HEAD
        .ride-card .card-img,
        .bike-card .card-img {
            background: rgba(0, 105, 62, 0.08);
            padding: 1.5rem;
            text-align: center;
        }
        .ride-card .card-img i,
        .bike-card .card-img i {
            font-size: 3.5rem;
            color: var(--mzuni-green);
        }

        /* ===== INFO CARDS - GLASSMORPHISM ===== */
=======

        /* ===== CARD IMAGES ===== */
        .ride-card .card-img,
        .bike-card .card-img {
            background: var(--mzuni-gray);
            padding: 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 200px;
        }
        .ride-card .card-img img,
        .bike-card .card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .ride-card .card-img .vehicle-type-badge,
        .bike-card .card-img .vehicle-type-badge {
            position: absolute;
            bottom: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
        }

        /* ===== SERVICE DESCRIPTION ===== */
        .service-description {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--mzuni-gold);
        }
        .service-description p {
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
            font-size: 0.95rem;
        }
        .service-description strong {
            color: var(--mzuni-gold);
        }

        /* ===== OWNER & VEHICLE DETAILS ===== */
        .ride-card .bg-light {
            background: rgba(255, 255, 255, 0.08) !important;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .ride-card .bg-light .fa-user-circle,
        .ride-card .bg-light .fa-car {
            opacity: 0.7;
        }
        .ride-card .bg-light .badge {
            font-size: 0.6rem;
            padding: 2px 8px;
        }
        .ride-card .bg-light .small {
            font-size: 0.8rem;
        }

        /* ===== INFO CARDS ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .info-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            color: var(--mzuni-text);
            border-left: 5px solid var(--mzuni-green);
            border-radius: 15px;
            padding: 1.5rem;
            height: 100%;
            transition: 0.3s;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        .info-card i {
            color: var(--mzuni-green);
        }

<<<<<<< HEAD
        /* ===== STAT CONTAINER - TRANSPARENT ===== */
=======
        /* ===== STAT CONTAINER ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .stat-container {
            background: rgba(0, 105, 62, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .stat-container h2 {
            font-weight: 800;
            font-size: 2.5rem;
            color: white;
        }
        .stat-container p {
            color: rgba(255,255,255,0.9);
        }

<<<<<<< HEAD
        /* ===== SECTIONS - TRANSPARENT ===== */
=======
        /* ===== SECTIONS ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .services-section,
        .about-section,
        .cta-section {
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
            border: 1px solid rgba(255,255,255,0.08);
        }

<<<<<<< HEAD
        /* ===== CTA SECTION ===== */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .cta-section {
            text-align: center;
            padding: 3rem;
        }
        .cta-section h3 {
            color: white;
            font-weight: 800;
        }
        .cta-section p {
            color: rgba(255,255,255,0.9);
            font-size: 1.2rem;
        }

        /* ===== BADGES ===== */
        .badge-mzuni {
            background: var(--mzuni-green);
            color: white;
            padding: 6px 14px;
            font-weight: 500;
        }
        .badge-gold {
            background: var(--mzuni-gold);
            color: var(--mzuni-dark);
            padding: 6px 14px;
            font-weight: 500;
        }

        /* ===== ACTION BUTTON GROUP ===== */
        .action-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .action-btn-group .btn {
            border-radius: 50px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
        }

        /* ===== MAP ===== */
        #map {
            height: 280px;
            border-radius: 15px;
            border: 2px solid rgba(255,255,255,0.2);
        }
        .btn-map {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 500;
            transition: 0.3s;
            color: var(--mzuni-text);
        }
        .btn-map:hover {
            background: var(--mzuni-green);
            color: white;
        }
        .btn-geolocate {
            background: var(--mzuni-green);
            border: none;
            border-radius: 40px;
            padding: 8px 14px;
            color: white;
            transition: 0.3s;
        }
        .btn-geolocate:hover {
            background: var(--mzuni-green-dark);
        }

<<<<<<< HEAD
        /* ===== LOCATION INPUT GROUP ===== */
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        .location-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .location-input-group .form-select {
            flex: 1;
        }

<<<<<<< HEAD
        /* ===== FOOTER - TRANSPARENT ===== */
=======
        /* ===== QR CODE MODAL ===== */
        #bikeQRModal .modal-content {
            border-radius: 20px;
            overflow: hidden;
        }
        #bikeQRModal .modal-header {
            background: linear-gradient(135deg, #00693E, #00843D);
        }
        #bikeQRModal .modal-body {
            padding: 2rem;
        }
        #bikeQRContainer img {
            border: 3px solid #00693E;
            border-radius: 12px;
            padding: 10px;
            background: white;
            max-width: 300px;
            width: 100%;
        }
        #bikeQRContainer .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        .qr-scan-btn {
            transition: all 0.3s ease;
        }
        .qr-scan-btn:hover {
            transform: scale(1.05);
        }

        /* ===== FOOTER ===== */
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        footer {
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(10px);
            color: rgba(255,255,255,0.8);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
        }
        footer a:hover {
            color: var(--mzuni-gold);
        }
        footer h5, footer h6 {
            color: white;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .tab-btn { padding: 0.5rem 1.2rem; font-size: 0.85rem; }
            .search-card { padding: 1.5rem; }
            .stat-container h2 { font-size: 1.8rem; }
            .section-title { font-size: 1.8rem; }
            .navbar-brand img { height: 35px; }
            .navbar-brand .brand-text { font-size: 1.2rem; }
<<<<<<< HEAD
        }

        /* ===== ANIMATIONS ===== */
=======
            .ride-card .card-img,
            .bike-card .card-img {
                height: 160px;
            }
            #bikeQRContainer img {
                max-width: 200px;
            }
        }

>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeInUp 0.6s ease forwards;
        }
<<<<<<< HEAD

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        /* Search icon floating */
        #searchCard {
            transition: all 0.3s ease;
        }
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    </style>
</head>
<body>

<div class="content-wrapper">
    <!-- ===== NAVBAR ===== -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
<<<<<<< HEAD
                <!-- MZUNI LOGO - Now properly displayed -->
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni University" height="45">
                <span class="brand-text">Mzuni <span class="brand-green">UNITRAS</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item">
                        <a href="#" id="navSearchIcon" class="nav-link" style="cursor: pointer;">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>
                    @guest
                        <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-outline-light btn-sm px-4">Login</a></li>
                        <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4">Sign Up</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-chart-pie me-2"></i> Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('user.bookings.index') }}">
                                    <i class="fas fa-calendar-check me-2"></i> My Bookings
                                </a></li>
<<<<<<< HEAD
=======
                                @if(Auth::user()->isVehicleOwner())
                                    <li><a class="dropdown-item" href="{{ route('payout.dashboard') }}">
                                        <i class="fas fa-wallet me-2"></i> Payout Dashboard
                                    </a></li>
                                @endif
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <!-- ===== HERO SECTION ===== -->
    <section id="home" class="hero">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6 animate-fade-in">
                    <div class="trust-badge mb-3">
                        <i class="fas fa-shield-alt me-2"></i> Trusted by Mzuzu University Community
                    </div>
                    <h1 class="hero-title">
                        Your Campus Ride, <br>
                        <span class="highlight">Just a Tap Away</span>
                    </h1>
                    <p class="hero-description mt-3">
                        Safe carpool & bike sharing for students, staff, and locals.
                        Browse freely – book only when you're ready.
                    </p>
                    <div class="action-btn-group mt-4">
<<<<<<< HEAD
                        <a href="{{ route('user.bikes.index') }}" class="btn btn-success btn-lg">
                            <i class="fas fa-bicycle me-2"></i> Pick a Bike
                        </a>
                        <a href="{{ route('search') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-car me-2"></i> Pick a Ride
                        </a>
                        @auth
                            <a href="{{ route('offer.ride') }}" class="btn btn-warning btn-lg">
                                <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-warning btn-lg">
=======
                        @auth
                            @if(Auth::user()->isVehicleOwner())
                                <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-bicycle me-2"></i> Add Bike
                                </a>
                            @else
                                <a href="{{ route('user.bikes.index') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-bicycle me-2"></i> Pick a Bike
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('user.bikes.index')) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-bicycle me-2"></i> Pick a Bike
                            </a>
                        @endauth

                        @auth
                            <a href="{{ route('search') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-car me-2"></i> Pick a Ride
                            </a>
                        @else
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('search')) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-car me-2"></i> Pick a Ride
                            </a>
                        @endauth

                        @auth
                            @if(Auth::user()->isVehicleOwner())
                                <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-warning btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i> Publish Ride
                                </a>
                            @else
                                <a href="{{ route('offer.ride') }}" class="btn btn-warning btn-lg">
                                    <i class="fas fa-plus-circle me-2"></i> Become Owner
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('offer.ride')) }}" class="btn btn-warning btn-lg">
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-6 animate-fade-in" style="animation-delay: 0.2s;">
                    <div class="search-card" id="searchCard">
                        <h5 class="mb-3">
                            <i class="fas fa-map-marked-alt" style="color: var(--mzuni-green);"></i>
                            Pick your route on the map
                        </h5>

                        <div class="map-buttons mb-3">
                            <button id="setFromBtn" class="btn-map">
                                <i class="fas fa-map-pin"></i> Set as "From"
                            </button>
                            <button id="setToBtn" class="btn-map">
                                <i class="fas fa-flag-checkered"></i> Set as "To"
                            </button>
                            <button id="clearMarkersBtn" class="btn-map">
                                <i class="fas fa-eraser"></i> Clear
                            </button>
                        </div>

                        <div id="map"></div>

                        <div class="mb-3 mt-3">
                            <label class="form-label">From (pickup)</label>
                            <div class="location-input-group">
                                <select id="searchFrom" class="form-select">
                                    <option value="">Select pickup location</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ request('from_location_id') == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button id="geolocateBtn" class="btn-geolocate" title="Use my current location">
                                    <i class="fas fa-location-dot"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3" id="toFieldWrapper">
                            <label class="form-label">To (destination)</label>
                            <select id="searchTo" class="form-select">
                                <option value="">Select destination</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ request('to_location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" id="bikeTypeWrapper" style="display: none;">
                            <label class="form-label">Bike type</label>
                            <select id="bikeTypeFilter" class="form-select">
                                <option value="">All types</option>
                                <option value="mountain">Mountain</option>
                                <option value="city">City</option>
                                <option value="hybrid">Hybrid</option>
                                <option value="electric">Electric</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="searchDate" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        <button class="btn btn-primary w-100 py-2" id="searchBtn">
                            <i class="fas fa-arrow-right me-2"></i> Search rides & bikes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== SERVICES SECTION ===== -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="services-section">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                    <div>
                        <h2 class="section-title">Our <span class="green">Services</span></h2>
                        <p class="section-subtitle">Choose how you want to move around campus</p>
                    </div>
                    <div class="service-tabs">
                        <button class="tab-btn active" id="tabCarpool" data-tab="carpool">
                            <i class="fas fa-car-side"></i> Carpool
                        </button>
                        <button class="tab-btn" id="tabBike" data-tab="bike">
                            <i class="fas fa-bicycle"></i> Bike sharing
                        </button>
                    </div>
                    @auth
<<<<<<< HEAD
                        <a href="{{ route('offer.ride') }}" class="btn btn-warning">
                            <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-warning">
=======
                        @if(Auth::user()->isVehicleOwner())
                            <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus-circle me-2"></i> Publish Ride
                            </a>
                        @else
                            <a href="{{ route('offer.ride') }}" class="btn btn-warning">
                                <i class="fas fa-plus-circle me-2"></i> Become Owner
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}?redirect_to={{ urlencode(route('offer.ride')) }}" class="btn btn-warning">
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                            <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                        </a>
                    @endauth
                </div>

<<<<<<< HEAD
                <!-- Carpool Panel -->
=======
                <!-- ===== SERVICE DESCRIPTION ===== -->
                <div class="service-description">
                    <p>
                        <strong>Carpool:</strong> Share rides with fellow students and staff. 
                        Save money, reduce traffic, and make new friends on your daily commute.
                        <br>
                        <strong>Bike Sharing:</strong> Rent a bike for quick campus travel. 
                        Pay only MWK 2 per minute - perfect for short trips between classes!
                    </p>
                </div>

                <!-- ===== CARPOOL PANEL ===== -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                <div id="carpoolPanel">
                    <div class="row g-4" id="ridesList">
                        @forelse($availableVehicles as $ride)
                            @php
                                $fromName = $ride->fromLocation->name ?? $ride->from_location ?? 'N/A';
                                $toName   = $ride->toLocation->name ?? $ride->to_location ?? 'N/A';
<<<<<<< HEAD
                            @endphp
                            <div class="col-lg-4 col-md-6 ride-item"
                                 data-ride-id="{{ $ride->id }}"
                                 data-from-id="{{ $ride->from_location_id ?? '' }}"
                                 data-to-id="{{ $ride->to_location_id ?? '' }}"
                                 data-date="{{ \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d') }}"
                                 data-price="{{ $ride->price }}">
                                <div class="ride-card">
                                    <div class="card-img">
                                        <i class="fas fa-car-side"></i>
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge badge-mzuni">
                                                {{ ucfirst(str_replace('_', ' ', $ride->ad_type ?? 'standard')) }}
                                            </span>
                                            <span class="price">MWK {{ number_format($ride->price, 0) }}</span>
                                        </div>
                                        <h5 class="fw-bold mt-2">{{ $fromName }} → {{ $toName }}</h5>
                                        <div class="text-muted small">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M Y, H:i') }}
                                            <span class="ms-2"><i class="fas fa-users"></i> {{ $ride->available_seats }} seats</span>
                                        </div>
                                        <div class="d-grid mt-3">
=======
                                $ownerName = $ride->owner->name ?? 'N/A';
                                $vehiclePlate = $ride->vehicle->license_plate ?? $ride->license_plate ?? 'N/A';
                                $vehicleName = $ride->vehicle->name ?? $ride->vehicle_name ?? 'Vehicle';
                                $vehicleModel = $ride->vehicle->model ?? $ride->model ?? '';
                                $isStaff = $ride->owner && $ride->owner->isStaff();
                            @endphp
                            <div class="col-lg-4 col-md-6 ride-item"
                                data-ride-id="{{ $ride->id }}"
                                data-from-id="{{ $ride->from_location_id ?? '' }}"
                                data-to-id="{{ $ride->to_location_id ?? '' }}"
                                data-date="{{ \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d') }}"
                                data-price="{{ $ride->price }}">
                                <div class="ride-card">
                                    <!-- ===== VEHICLE IMAGE ===== -->
                                    <div class="card-img">
                                        @if($ride->vehicle && $ride->vehicle->image)
                                            <img src="{{ asset('storage/' . $ride->vehicle->image) }}" alt="{{ $vehicleName }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('images/fallbacks/vehicle-placeholder.jpg') }}" alt="Vehicle Placeholder" loading="lazy">
                                        @endif
                                        <span class="vehicle-type-badge">
                                            <i class="fas fa-car"></i> {{ $ride->available_seats ?? 0 }} seats
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <!-- ===== ROUTE & PRICE ===== -->
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge badge-mzuni">
                                                <i class="fas fa-route me-1"></i> {{ ucfirst(str_replace('_', ' ', $ride->ad_type ?? 'standard')) }}
                                            </span>
                                            <span class="price">MWK {{ number_format($ride->price, 0) }}</span>
                                        </div>
                                        
                                        <h5 class="fw-bold mt-2">{{ $fromName }} → {{ $toName }}</h5>
                                        
                                        <!-- ===== DATE & TIME ===== -->
                                        <p class="text-muted small mb-1">
                                            <i class="far fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M Y, H:i') }}
                                        </p>
                                        
                                        <!-- ===== SEATS AVAILABLE ===== -->
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-users"></i> {{ $ride->available_seats }} seats available
                                        </p>

                                        <!-- ===== OWNER & VEHICLE DETAILS ===== -->
                                        <div class="bg-light rounded p-2 mb-3" style="background: rgba(0,0,0,0.05) !important;">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <i class="fas fa-user-circle" style="color: var(--mzuni-green);"></i>
                                                <span class="small fw-semibold">{{ $ownerName }}</span>
                                                @if($isStaff)
                                                    <span class="badge bg-success" style="font-size: 0.6rem;">
                                                        <i class="fas fa-graduation-cap me-1"></i> MZUNI Staff
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-car" style="color: var(--mzuni-green);"></i>
                                                <span class="small">{{ $vehicleName }} {{ $vehicleModel }}</span>
                                                <span class="badge bg-secondary" style="font-size: 0.6rem;">
                                                    {{ $vehiclePlate }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- ===== HOP IN BUTTON ===== -->
                                        <div class="d-grid mt-2">
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                            @auth
                                                <a href="{{ route('user.bookings.create', $ride) }}" class="btn btn-primary">
                                                    Hop In <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            @else
<<<<<<< HEAD
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                    Hop In <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
=======
                                                <a href="{{ route('login') }}?redirect_to={{ urlencode(route('user.bookings.create', $ride)) }}" class="btn btn-primary">
                                                    Hop In <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <h5 style="color: white;">No rides available</h5>
                                <p style="color: rgba(255,255,255,0.7);">Check back later or offer your own ride!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

<<<<<<< HEAD
                <!-- Bike Panel -->
=======
                <!-- ===== BIKE PANEL WITH QR CODE ===== -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                <div id="bikePanel" style="display: none;">
                    <div class="row g-4" id="bikesList">
                        @forelse($availableBikes as $bike)
                            <div class="col-lg-3 col-md-6 bike-item"
                                 data-bike-id="{{ $bike->id }}"
                                 data-location-id="{{ $bike->location_id ?? '' }}"
                                 data-type="{{ strtolower($bike->type) }}">
                                <div class="bike-card">
<<<<<<< HEAD
                                    <div class="card-img">
                                        <i class="fas fa-bicycle"></i>
                                    </div>
                                    <div class="p-3">
                                        <h5 class="fw-bold">{{ $bike->brand }} {{ $bike->model }}</h5>
                                        <div class="text-muted small">{{ ucfirst($bike->type) }} Bike</div>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span>Rate:</span>
=======
                                    <!-- ===== BIKE IMAGE WITH FALLBACK ===== -->
                                    <div class="card-img">
                                        @if($bike->image)
                                            <img src="{{ asset('storage/' . $bike->image) }}" alt="{{ $bike->brand }} {{ $bike->model }}" loading="lazy">
                                        @else
                                            <img src="{{ asset('images/fallbacks/bike-placeholder.jpg') }}" alt="Bike Placeholder" loading="lazy">
                                        @endif
                                        <span class="vehicle-type-badge">
                                            <i class="fas fa-bicycle"></i> {{ ucfirst($bike->type ?? 'standard') }}
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <h5 class="fw-bold">{{ $bike->brand }} {{ $bike->model }}</h5>
                                        <p class="text-muted small">{{ ucfirst($bike->type) }} Bike</p>
                                        <div class="d-flex justify-content-between mt-2">
                                            <span><i class="fas fa-clock"></i> Rate:</span>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                            <strong class="price">MWK 2/min</strong>
                                        </div>
                                        <span class="badge badge-mzuni mb-2 d-inline-block">
                                            <i class="fas fa-check-circle"></i> Available now
                                        </span>
<<<<<<< HEAD
                                        <div class="d-grid">
=======
                                        
                                        <!-- ===== QR CODE BUTTON + RENT BUTTON ===== -->
                                        <div class="d-grid gap-2">
                                            <!-- QR Code Scan Button -->
                                            @auth
                                                <button class="btn btn-success qr-scan-btn" onclick="showBikeQR('{{ $bike->id }}', '{{ $bike->brand }} {{ $bike->model }}')">
                                                    <i class="fas fa-qrcode me-2"></i> Scan to Activate
                                                </button>
                                            @else
                                                <a href="{{ route('login') }}?redirect_to={{ urlencode(route('user.bikes.rent', $bike)) }}" class="btn btn-success qr-scan-btn">
                                                    <i class="fas fa-qrcode me-2"></i> Scan to Activate
                                                </a>
                                            @endauth

                                            <!-- Rent Now Button -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                            @auth
                                                <a href="{{ route('user.bikes.rent', $bike) }}" class="btn btn-primary">
                                                    Rent now <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            @else
<<<<<<< HEAD
                                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                                                    Rent now <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
=======
                                                <a href="{{ route('login') }}?redirect_to={{ urlencode(route('user.bikes.rent', $bike)) }}" class="btn btn-primary">
                                                    Rent now <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-bicycle fa-3x text-muted mb-3"></i>
                                <h5 style="color: white;">No bikes available</h5>
                                <p style="color: rgba(255,255,255,0.7);">All bikes are currently rented. Check back later!</p>
                            </div>
                        @endforelse
                    </div>
                </div>

<<<<<<< HEAD
                <!-- Info Cards -->
=======
                <!-- ===== INFO CARDS ===== -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="info-card">
                            <i class="fas fa-route fa-2x mb-2"></i>
                            <h6>Popular Routes</h6>
                            <p class="small text-muted mb-0">
                                <strong>MZUNI Main Gate</strong> → Mzuzu Town<br>
                                <span class="badge badge-mzuni mt-1">MWK 2,500</span>
                            </p>
                            <p class="small text-muted mt-2 mb-0">
                                <strong>Luwinga</strong> → MZUNI Library<br>
                                <span class="badge badge-mzuni mt-1">MWK 1,000</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="info-card">
                            <i class="fas fa-shield-alt fa-2x mb-2"></i>
                            <h6>Safety First</h6>
                            <p class="small text-muted mb-0">
                                ✅ All vehicles and bikes are verified<br>
                                🛡️ 24/7 support available<br>
                                📍 Live tracking on every ride
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="info-card">
                            <i class="fas fa-wallet fa-2x mb-2"></i>
                            <h6>Estimated Savings</h6>
                            <p class="small text-muted mb-0">
                                💰 Carpool saves up to 70%<br>
                                🚲 Bike rental just MWK 2/minute<br>
                                <span class="badge-gold mt-1 d-inline-block px-2 py-1">🌱 Eco-friendly</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== ABOUT & STATS ===== -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="about-section">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="badge-gold mb-2 px-3 py-2 rounded-pill d-inline-block">
                            <i class="fas fa-leaf me-1"></i> Eco-friendly
                        </span>
                        <h2 class="section-title">Unified Transport for <span class="green">Mzuzu University</span></h2>
                        <p class="section-subtitle">
                            UNITRAS connects Staff (vehicle owners) and passengers in a seamless,
                            affordable and sustainable ecosystem.
                        </p>
                        @guest
<<<<<<< HEAD
                            <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-user-plus me-2"></i> Join community
                            </button>
=======
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('offer.ride')) }}" class="btn btn-primary rounded-pill mt-3 px-4">
                                <i class="fas fa-user-plus me-2"></i> Join community
                            </a>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                        @else
                            <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill mt-3 px-4">
                                <i class="fas fa-chart-pie me-2"></i> Go to Dashboard
                            </a>
                        @endguest
                    </div>
                    <div class="col-lg-6">
                        <div class="stat-container text-white p-4 rounded-4 shadow">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h2 class="fw-bold">{{ number_format($stats['total_vehicles'] ?? 0) }}+</h2>
                                    <p class="mb-0">Vehicles</p>
                                </div>
                                <div class="col-4">
                                    <h2 class="fw-bold">{{ number_format($stats['total_users'] ?? 0) }}+</h2>
                                    <p class="mb-0">Users</p>
                                </div>
                                <div class="col-4">
                                    <h2 class="fw-bold">{{ number_format($stats['completed_trips'] ?? 0) }}+</h2>
                                    <p class="mb-0">Trips</p>
                                </div>
                            </div>
                            <hr class="bg-white opacity-25">
                            <p class="mb-0 text-center small">
                                <i class="fas fa-map-marked-alt me-1"></i>
                                Covering Mzuzu University main campus, Luwinga, Dunduzu Campus,
                                Mzuzu Town and Chibavi and surrounding areas
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="py-5">
        <div class="container">
            <div class="cta-section">
                <h3>Ready to share the <span class="highlight">journey</span>?</h3>
                <p>Join Mzuni UNITRAS today — offer a seat, rent a bike.</p>
                @guest
<<<<<<< HEAD
                    <button class="btn btn-primary rounded-pill px-5 py-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-user-plus me-2"></i> Create free account
                    </button>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill px-5 py-3">
                        <i class="fas fa-chart-pie me-2"></i> Go to Dashboard
                    </a>
=======
                    <a href="{{ route('login') }}?redirect_to={{ urlencode(route('offer.ride')) }}" class="btn btn-primary rounded-pill px-5 py-3">
                        <i class="fas fa-user-plus me-2"></i> Create free account
                    </a>
                @else
                    @if(Auth::user()->isVehicleOwner())
                        <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-primary rounded-pill px-5 py-3">
                            <i class="fas fa-plus-circle me-2"></i> Publish a Ride
                        </a>
                    @else
                        <a href="{{ route('offer.ride') }}" class="btn btn-primary rounded-pill px-5 py-3">
                            <i class="fas fa-user-plus me-2"></i> Become an Owner
                        </a>
                    @endif
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                @endguest
            </div>
        </div>
    </section>

<<<<<<< HEAD
=======
    <!-- ===== QR CODE MODAL ===== -->
    <div class="modal fade" id="bikeQRModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-qrcode me-2"></i> <span id="bikeQRTitle">Scan to Activate Bike</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <!-- QR Code Container -->
                    <div id="bikeQRContainer" class="mb-3">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="alert alert-info text-start">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How to activate:</strong>
                        <ol class="mt-2 mb-0">
                            <li>Open your phone's camera or QR scanner app</li>
                            <li>Scan the QR code displayed above</li>
                            <li>You will be redirected to activate this bike instantly</li>
                        </ol>
                    </div>

                    <!-- Alternative: Manual Activation Link -->
                    <div class="mt-3">
                        <p class="text-muted small">Or click the button below to activate directly:</p>
                        <a href="#" id="bikeDirectActivate" class="btn btn-primary">
                            <i class="fas fa-bicycle me-2"></i> Activate Bike Now
                        </a>
                    </div>

                    <!-- Download QR Code -->
                    <div class="mt-2">
                        <button onclick="downloadBikeQR()" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download me-1"></i> Download QR Code
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    <!-- ===== FOOTER ===== -->
    <footer class="pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-bus me-2"></i>Mzuni UNITRAS</h5>
                    <p class="text-muted small">Mzuzu University, Luwinga</p>
                    <p class="text-muted small">
                        <i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw<br>
                        <i class="fas fa-phone me-2"></i>+265 990 179 811
                    </p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Rides</a></li>
                        <li><a href="#services">Bikes</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>For Owners</h6>
                    <ul class="list-unstyled small">
<<<<<<< HEAD
                        <li><a href="{{ route('offer.ride') }}">Offer a Ride</a></li>
                        <li><a href="#">Add Vehicle</a></li>
                        <li><a href="#">My Vehicles</a></li>
=======
                        @auth
                            @if(Auth::user()->isVehicleOwner())
                                <li><a href="{{ route('vehicle-owner.advertisements.create') }}">Publish Ride</a></li>
                                <li><a href="{{ route('vehicle-owner.vehicles.index') }}">My Vehicles</a></li>
                                <li><a href="{{ route('payout.dashboard') }}">Payout Dashboard</a></li>
                            @else
                                <li><a href="{{ route('offer.ride') }}">Become an Owner</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}?redirect_to={{ urlencode(route('offer.ride')) }}">Become an Owner</a></li>
                        @endauth
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h6>Social</h6>
                    <div class="d-flex gap-3">
                        <a href="#"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-star" style="color: var(--mzuni-gold);"></i>
                        Rated 4.8/5 by users
                    </p>
                </div>
            </div>
            <hr class="opacity-25">
            <div class="text-center text-muted small">
                &copy; {{ date('Y') }} Mzuni UNITRAS — browse freely, book after login.
                <br>
                <span class="badge badge-mzuni">
                    <i class="fas fa-shield-alt"></i> Secure Payments
                </span>
            </div>
        </div>
    </footer>
<<<<<<< HEAD

    <!-- ===== LOGIN MODAL ===== -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-body p-4 text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="fas fa-lock fa-2x" style="color: var(--mzuni-green);"></i>
                    </div>
                    <h4 class="fw-bold" style="color: var(--mzuni-dark);">Sign in to continue</h4>
                    <p class="text-muted">
                        You're one step away from booking a ride, renting a bike, or offering a seat.<br>
                        Create an account or log in.
                    </p>
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill py-2">
                            <i class="fas fa-sign-in-alt me-2"></i> Log in
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="fas fa-user-plus me-2"></i> Create new account
                        </a>
                    </div>
                    <p class="small text-muted mt-3">
                        ⚡ Browse everything, only register to book/offer.
                    </p>
                </div>
            </div>
        </div>
    </div>
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
</div>

<!-- ===== SCRIPTS ===== -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ============================================
    // LOCATION MAPPING
    // ============================================
    function buildLocationMap() {
        const map = {};
        document.querySelectorAll('#searchFrom option, #searchTo option').forEach(opt => {
            if (opt.value) {
                map[opt.textContent.trim().toLowerCase()] = opt.value;
            }
        });
        return map;
    }
    const locationNameToId = buildLocationMap();

    function setSelectValue(selectId, locationName) {
        const select = document.getElementById(selectId);
        const normalized = locationName.trim().toLowerCase();
        if (locationNameToId[normalized]) {
            select.value = locationNameToId[normalized];
        } else {
            let found = false;
            for (const [name, id] of Object.entries(locationNameToId)) {
                if (name.includes(normalized) || normalized.includes(name)) {
                    select.value = id;
                    found = true;
                    break;
                }
            }
            if (!found) {
                select.value = '';
                alert('Location "' + locationName + '" not in our list. Please select from dropdown.');
            }
        }
    }

    // ============================================
    // LEAFET MAP
    // ============================================
    const map = L.map('map').setView([-11.45, 34.02], 14);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB'
    }).addTo(map);

    let fromMarker = null, toMarker = null;
    let fromLatLng = null, toLatLng = null;
    let polyline = null;
    let mapClickMode = 'from';

    async function reverseGeocode(lat, lng, callback) {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
            const data = await res.json();
            const name = data.display_name.split(',')[0];
            callback(name);
        } catch {
            callback(`${lat.toFixed(4)}, ${lng.toFixed(4)}`);
        }
    }

    function setFromLocation(lat, lng, name) {
        fromLatLng = { lat, lng };
        if (fromMarker) map.removeLayer(fromMarker);
        fromMarker = L.marker([lat, lng]).addTo(map).bindPopup('Pickup').openPopup();
        setSelectValue('searchFrom', name);
        drawPolyline();
        setTimeout(() => filterBySearch(), 100);
    }

    function setToLocation(lat, lng, name) {
        toLatLng = { lat, lng };
        if (toMarker) map.removeLayer(toMarker);
        toMarker = L.marker([lat, lng]).addTo(map).bindPopup('Destination').openPopup();
        setSelectValue('searchTo', name);
        drawPolyline();
        setTimeout(() => filterBySearch(), 100);
    }

    function drawPolyline() {
        if (polyline) map.removeLayer(polyline);
        if (fromLatLng && toLatLng) {
            polyline = L.polyline([[fromLatLng.lat, fromLatLng.lng], [toLatLng.lat, toLatLng.lng]], {
                color: '#00693E',
                weight: 4,
                opacity: 0.7
            }).addTo(map);
            map.fitBounds(polyline.getBounds());
        }
    }

    function clearMapSelection() {
        if (fromMarker) map.removeLayer(fromMarker);
        if (toMarker) map.removeLayer(toMarker);
        if (polyline) map.removeLayer(polyline);
        fromMarker = toMarker = null;
        fromLatLng = toLatLng = null;
        document.getElementById('searchFrom').value = '';
        document.getElementById('searchTo').value = '';
        filterBySearch();
    }

    document.getElementById('setFromBtn').onclick = () => {
        mapClickMode = 'from';
        alert('Click on the map to set pickup location');
    };
    document.getElementById('setToBtn').onclick = () => {
        mapClickMode = 'to';
        alert('Click on the map to set destination');
    };
    document.getElementById('clearMarkersBtn').onclick = clearMapSelection;

    map.on('click', async (e) => {
        const { lat, lng } = e.latlng;
        reverseGeocode(lat, lng, (name) => {
            if (mapClickMode === 'from') setFromLocation(lat, lng, name);
            else setToLocation(lat, lng, name);
        });
    });

    document.getElementById('geolocateBtn').onclick = () => {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }
        navigator.geolocation.getCurrentPosition(async (pos) => {
            const { latitude, longitude } = pos.coords;
            reverseGeocode(latitude, longitude, (name) => {
                setFromLocation(latitude, longitude, name);
                map.setView([latitude, longitude], 15);
            });
        }, () => alert('Unable to retrieve your location.'));
    };

    // ============================================
    // TAB SWITCHING
    // ============================================
    const tabCarpool = document.getElementById('tabCarpool');
    const tabBike = document.getElementById('tabBike');
    const carpoolPanel = document.getElementById('carpoolPanel');
    const bikePanel = document.getElementById('bikePanel');
    const searchDate = document.getElementById('searchDate');
    const searchBtn = document.getElementById('searchBtn');
    const toFieldWrapper = document.getElementById('toFieldWrapper');
    const bikeTypeWrapper = document.getElementById('bikeTypeWrapper');
    const bikeTypeFilter = document.getElementById('bikeTypeFilter');

    function switchTab(tab) {
        if (tab === 'carpool') {
            carpoolPanel.style.display = 'block';
            bikePanel.style.display = 'none';
            tabCarpool.classList.add('active');
            tabBike.classList.remove('active');
            toFieldWrapper.style.display = 'block';
            bikeTypeWrapper.style.display = 'none';
        } else {
            carpoolPanel.style.display = 'none';
            bikePanel.style.display = 'block';
            tabBike.classList.add('active');
            tabCarpool.classList.remove('active');
            toFieldWrapper.style.display = 'none';
            bikeTypeWrapper.style.display = 'block';
        }
        filterBySearch();
    }

    tabCarpool.addEventListener('click', () => switchTab('carpool'));
    tabBike.addEventListener('click', () => switchTab('bike'));

    // ============================================
    // FILTERING
    // ============================================
    function filterRides() {
        const fromId = document.getElementById('searchFrom').value;
        const toId = document.getElementById('searchTo').value;
        const dateVal = searchDate.value;
        const rideItems = document.querySelectorAll('#ridesList .ride-item');
        let visibleCount = 0;
        rideItems.forEach(item => {
            const fromAttr = item.getAttribute('data-from-id') || '';
            const toAttr = item.getAttribute('data-to-id') || '';
            const itemDate = item.getAttribute('data-date') || '';
            let show = true;
            if (fromId && fromAttr !== fromId) show = false;
            if (toId && toAttr !== toId) show = false;
            if (dateVal && itemDate !== dateVal) show = false;
            item.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        showNoResultsMessage('ridesList', visibleCount, 'rides');
    }

    function filterBikes() {
        const locId = document.getElementById('searchFrom').value;
        const typeVal = bikeTypeFilter.value.toLowerCase();
        const bikeItems = document.querySelectorAll('#bikesList .bike-item');
        let visibleCount = 0;
        bikeItems.forEach(item => {
            const locationAttr = item.getAttribute('data-location-id') || '';
            const bikeTypeAttr = (item.getAttribute('data-type') || '').toLowerCase();
            let show = true;
            if (locId && locationAttr !== locId) show = false;
            if (typeVal && bikeTypeAttr !== typeVal) show = false;
            item.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        showNoResultsMessage('bikesList', visibleCount, 'bikes');
    }

    function showNoResultsMessage(containerId, visibleCount, type) {
        const container = document.getElementById(containerId);
        const existingMsg = document.getElementById(`no${type}Msg`);
        if (visibleCount === 0 && container.children.length > 0) {
            if (!existingMsg) {
                const msg = document.createElement('div');
                msg.id = `no${type}Msg`;
                msg.className = 'col-12 text-center py-4';
                msg.innerHTML = `
                    <i class="fas fa-search fa-2x text-muted mb-2"></i>
                    <p style="color: rgba(255,255,255,0.7);">No ${type} match your search. Try different filters.</p>
                `;
                container.parentNode.appendChild(msg);
            } else {
                existingMsg.style.display = 'block';
            }
        } else if (existingMsg) {
            existingMsg.style.display = 'none';
        }
    }

    function filterBySearch() {
        if (carpoolPanel.style.display !== 'none') {
            filterRides();
        } else {
            filterBikes();
        }
    }

    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        filterBySearch();
    });

    document.getElementById('searchFrom').addEventListener('change', filterBySearch);
    document.getElementById('searchTo').addEventListener('change', filterBySearch);
    searchDate.addEventListener('change', filterBySearch);
    bikeTypeFilter.addEventListener('change', () => {
        if (bikePanel.style.display !== 'none') filterBikes();
    });

    document.getElementById('navSearchIcon').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('searchCard').scrollIntoView({ behavior: 'smooth' });
    });

    // ============================================
<<<<<<< HEAD
    // INITIAL LOAD
    // ============================================
    setTimeout(filterBySearch, 500);
=======
    // BIKE QR CODE FUNCTIONS
    // ============================================
    let currentBikeQRData = null;

    /**
     * Show QR code for a specific bike
     */
    function showBikeQR(bikeId, bikeName) {
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('bikeQRModal'));
        
        // Update title
        document.getElementById('bikeQRTitle').textContent = 'Scan to Activate: ' + bikeName;
        
        // Show loading spinner
        document.getElementById('bikeQRContainer').innerHTML = `
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        `;

        // Open modal
        modal.show();

        // Fetch QR code from server
        fetch(`/bike/qr/${bikeId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentBikeQRData = data;
                    
                    // Display QR code
                    document.getElementById('bikeQRContainer').innerHTML = `
                        <img src="${data.qr_code_url}" alt="QR Code" class="img-fluid">
                        <p class="text-muted small mt-2">
                            <i class="fas fa-bicycle me-1"></i> Bike ID: ${data.bike_id}
                        </p>
                    `;
                    
                    // Update activation link
                    document.getElementById('bikeDirectActivate').href = data.activation_url;
                } else {
                    document.getElementById('bikeQRContainer').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${data.message || 'Failed to load QR code'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                document.getElementById('bikeQRContainer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading QR code. Please try again.
                    </div>
                `;
                console.error('Error loading QR code:', error);
            });
    }

    /**
     * Download QR code as image
     */
    function downloadBikeQR() {
        if (!currentBikeQRData) {
            alert('No QR code to download. Please scan the QR code first.');
            return;
        }

        // Create a download link
        const link = document.createElement('a');
        link.href = currentBikeQRData.qr_code_url;
        link.download = `bike-qr-${currentBikeQRData.bike_id}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // ============================================
    // INITIAL LOAD
    // ============================================
    setTimeout(filterBySearch, 500);

    // ============================================
    // MOBILE QR SCANNER SUPPORT
    // ============================================
    function isMobile() {
        return /Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent);
    }

    // Check if running on mobile and add scanner hint
    document.addEventListener('DOMContentLoaded', function() {
        if (isMobile()) {
            // Add a floating scan button for mobile
            const scanBtn = document.createElement('div');
            scanBtn.className = 'position-fixed bottom-0 end-0 m-4';
            scanBtn.style.zIndex = '1000';
            scanBtn.innerHTML = `
                <button class="btn btn-success btn-lg rounded-circle shadow-lg" onclick="alert('📱 Use your phone\'s camera app to scan QR codes displayed on bike cards.')" style="width: 70px; height: 70px;">
                    <i class="fas fa-camera fa-2x"></i>
                </button>
            `;
            document.body.appendChild(scanBtn);
        }
    });
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
</script>
</body>
</html>