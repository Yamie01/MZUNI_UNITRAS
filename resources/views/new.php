<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Mzuni UNITRAS - Unified Transport System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #00529b;
            --primary-dark: #003f75;
            --secondary: #ff6b35;
            --accent: #00a896;
            --dark: #1a1a2e;
            --light: #f8f9fa;
            --gray: #6c757d;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            overflow-x: hidden;
        }
        
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 15px 0;
            transition: all 0.3s ease;
        }
        
        .navbar-scrolled {
            background: white;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary);
        }
        
        .nav-link {
            font-weight: 500;
            color: #333;
            transition: color 0.3s;
            margin: 0 5px;
        }
        
        .nav-link:hover {
            color: var(--primary);
        }
        
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            min-height: 90vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin-top: 70px;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .search-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            position: relative;
            z-index: 10;
        }
        
        .search-card h5 {
            color: #333;
            font-weight: 600;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .section-subtitle {
            color: var(--gray);
            margin-bottom: 3rem;
            font-size: 1.1rem;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 35px 25px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #eef2f6;
            cursor: pointer;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 82, 155, 0.12);
            border-color: transparent;
        }
        
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }
        
        .feature-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .feature-card h4 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .vehicle-card, .bike-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            cursor: pointer;
            height: 100%;
        }
        
        .vehicle-card:hover, .bike-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 35px rgba(0, 82, 155, 0.15);
        }
        
        .vehicle-img, .bike-img {
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .vehicle-badge, .bike-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(5px);
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .vehicle-info, .bike-info {
            padding: 20px;
        }
        
        .vehicle-title, .bike-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .vehicle-route, .bike-details {
            color: var(--gray);
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        
        .vehicle-price, .bike-price {
            font-weight: 800;
            color: var(--primary);
            font-size: 1.3rem;
        }
        
        .btn-book, .btn-rent {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        
        .btn-book:hover, .btn-rent:hover {
            transform: translateX(5px);
            color: white;
            background: var(--primary-dark);
        }
        
        .cta-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 60px 40px;
            border-radius: 30px;
            margin: 50px 0;
        }
        
        .footer {
            background: #1a1a2e;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-links a {
            color: #aaa;
            text-decoration: none;
            transition: color 0.3s;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .social-icons a {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            color: white;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px;
            background: var(--light);
            border-radius: 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero p {
                font-size: 1rem;
            }
            .section-title {
                font-size: 1.8rem;
            }
            .cta-section {
                padding: 40px 20px;
            }
            .stat-number {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-bus me-2"></i>Mzuni UNITRAS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#vehicles">Rides</a></li>
                    <li class="nav-item"><a class="nav-link" href="#bikes">Bikes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item ms-lg-3"><a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4">Login</a></li>
                    <li class="nav-item ms-2"><a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4">Sign Up</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="home" class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0 animate-fade-in">
                    <h1>Your Campus Ride<br>Is Just a Tap Away</h1>
                    <p>Safe, reliable, and affordable transportation for Mzuzu University students, staff, and the community.</p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4"><i class="fas fa-user-plus me-2"></i>Get Started</a>
                        <a href="#services" class="btn btn-outline-light btn-lg px-4"><i class="fas fa-play-circle me-2"></i>Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="search-card">
                        <h5 class="mb-3">Where are you going?</h5>
                        <form action="{{ route('search') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Pickup Location</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                    <input type="text" name="from" class="form-control border-0 bg-light" placeholder="Your current location">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Destination</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-flag-checkered text-primary"></i></span>
                                    <input type="text" name="to" class="form-control border-0 bg-light" placeholder="Where to?">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small">Travel Date</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-calendar text-primary"></i></span>
                                    <input type="date" name="date" class="form-control border-0 bg-light" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2"><i class="fas fa-search me-2"></i>Find a Ride</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">How Mzuni UNITRAS Works</h2>
                <p class="section-subtitle">Simple, fast, and convenient campus transportation</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card" onclick="location.href='{{ route('register') }}'">
                        <div class="feature-icon"><i class="fas fa-user-plus"></i></div>
                        <h4>Create Account</h4>
                        <p class="text-muted">Sign up as a student, staff, or vehicle owner in minutes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" onclick="location.href='{{ route('search') }}'">
                        <div class="feature-icon"><i class="fas fa-search"></i></div>
                        <h4>Find Your Ride</h4>
                        <p class="text-muted">Browse available vehicles, buses, or bikes near you</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card" onclick="location.href='{{ route('register') }}'">
                        <div class="feature-icon"><i class="fas fa-credit-card"></i></div>
                        <h4>Book & Pay</h4>
                        <p class="text-muted">Secure payment with mobile money or card options</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Available Rides Section -->
    <section id="vehicles" class="py-5 bg-light">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Available Rides Near You</h2>
                <p class="section-subtitle">Choose from our wide range of transportation options</p>
            </div>
            <div class="row g-4">
                @forelse($availableVehicles as $vehicle)
                <div class="col-lg-4 col-md-6">
                    <div class="vehicle-card" onclick="location.href='{{ route('login') }}'">
                        <div class="vehicle-img" style="background-image: url('{{ $vehicle->images && is_array($vehicle->images) && count($vehicle->images) > 0 ? asset('storage/' . $vehicle->images[0]) : 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}');">
                            <span class="vehicle-badge">{{ ucfirst(str_replace('_', ' ', $vehicle->ad_type)) }}</span>
                        </div>
                        <div class="vehicle-info">
                            <h5 class="vehicle-title">{{ $vehicle->from_location }} → {{ $vehicle->to_location }}</h5>
                            <p class="vehicle-route"><i class="fas fa-clock me-1"></i> {{ \Carbon\Carbon::parse($vehicle->departure_time)->format('d M Y, H:i') }}</p>
                            <p class="vehicle-route"><i class="fas fa-car me-1"></i> {{ $vehicle->vehicle->model ?? 'Vehicle' }} • {{ $vehicle->available_seats }} seats left</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="vehicle-price">MWK {{ number_format($vehicle->price, 0) }}</span>
                                <button class="btn-book">Book Now <i class="fas fa-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-car"></i>
                        <h4>No Rides Available</h4>
                        <p class="text-muted">Check back later for available rides near you</p>
                        <a href="{{ route('register') }}" class="btn btn-primary mt-3">Sign Up to Book Rides</a>
                    </div>
                </div>
                @endforelse
            </div>
            @if($availableVehicles && $availableVehicles->count() > 0)
            <div class="text-center mt-4">
                <a href="{{ route('search') }}" class="btn btn-outline-primary btn-lg px-5">View All Vehicles <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            @endif
        </div>
    </section>

    <!-- Available Bikes Section -->
    <section id="bikes" class="py-5">
        <div class="container">
            <div class="text-center">
                <h2 class="section-title">Rent a Bike</h2>
                <p class="section-subtitle">Eco-friendly and affordable bike rentals for campus commute</p>
            </div>
            <div class="row g-4">
                @forelse($availableBikes as $bike)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="bike-card" onclick="location.href='{{ route('login') }}'">
                        <!---<div class="bike-img" style="background-image: url('{{ $bike->images && is_array($bike->images) && count($bike->images) > 0 ? asset('storage/' . $bike->images[0]) : 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}');">-->
                            <span class="bike-badge">{{ ucfirst($bike->type) }}</span>
                        </div>
                        <div class="bike-info">
                            <h5 class="bike-title">{{ $bike->brand }} {{ $bike->model }}</h5>
                            <p class="bike-details"><i class="fas fa-tag me-1"></i> {{ ucfirst($bike->type) }} Bike</p>
                            <div class="d-flex justify-content-between mb-2">
                                <span><small>Hourly:</small> <strong>MWK {{ number_format($bike->price_per_hour, 0) }}</strong></span>
                                <span><small>Daily:</small> <strong>MWK {{ number_format($bike->price_per_day, 0) }}</strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="bike-price">Deposit: MWK {{ number_format($bike->deposit_amount, 0) }}</span>
                                <button class="btn-rent">Rent Now <i class="fas fa-arrow-right ms-1"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-bicycle"></i>
                        <h4>No Bikes Available</h4>
                        <p class="text-muted">Check back later for available bikes</p>
                        <a href="{{ route('register') }}" class="btn btn-primary mt-3">Sign Up to Rent Bikes</a>
                    </div>
                </div>
                @endforelse
            </div>
            @if($availableBikes && $availableBikes->count() > 0)
            <div class="text-center mt-4">
                <a href="{{ route('user.bikes.index') }}" class="btn btn-outline-primary btn-lg px-5">View All Bikes <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
            @endif
        </div>
    </section>

    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-4 shadow" alt="About UNITRAS">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title mb-3">About Mzuni UNITRAS</h2>
                    <p class="lead text-primary fw-semibold">Mzuzu University Unified Transport System</p>
                    <p>UNITRAS is a comprehensive transportation platform designed to serve the Mzuzu University community. We connect students, staff, and vehicle owners through a safe, reliable, and efficient system.</p>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle text-success fs-4 me-3"></i>
                                <div><h6 class="fw-bold mb-0">Verified Drivers</h6><small class="text-muted">All drivers vetted</small></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt text-success fs-4 me-3"></i>
                                <div><h6 class="fw-bold mb-0">Safe & Secure</h6><small class="text-muted">Real-time tracking</small></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-wallet text-success fs-4 me-3"></i>
                                <div><h6 class="fw-bold mb-0">Multiple Payments</h6><small class="text-muted">Mobile money & card</small></div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-headset text-success fs-4 me-3"></i>
                                <div><h6 class="fw-bold mb-0">24/7 Support</h6><small class="text-muted">Always here to help</small></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - FIXED (Removed the problematic 4th column) -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h2 class="stat-number">{{ number_format($stats['total_vehicles'] ?? 0) }}+</h2>
                    <p class="mb-0">Registered Vehicles</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h2 class="stat-number">{{ number_format($stats['total_users'] ?? 0) }}+</h2>
                    <p class="mb-0">Active Users</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h2 class="stat-number">{{ number_format($stats['completed_trips'] ?? 0) }}+</h2>
                    <p class="mb-0">Completed Rides</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="cta-section text-center">
            <h2 class="fw-bold mb-3">Ready to Ride?</h2>
            <p class="mb-4">Join thousands of students and staff using Mzuni UNITRAS daily</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5"><i class="fas fa-user-plus me-2"></i>Create Free Account</a>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="fas fa-bus me-2"></i>Mzuni UNITRAS</h5>
                    <p class="text-muted mt-3">Mzuzu University Unified Transport System - Connecting our community through smart transportation solutions.</p>
                    <div class="social-icons mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#vehicles">Rides</a></li>
                        <li><a href="#bikes">Bikes</a></li>
                        <li><a href="#about">About</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6>Transport Options</h6>
                    <ul class="list-unstyled footer-links">
                        <li><a href="{{ route('search') }}">Ride Sharing</a></li>
                        <li><a href="{{ route('search') }}">Taxi Services</a></li>
                        <li><a href="{{ route('search') }}">Bus Booking</a></li>
                        <li><a href="{{ route('user.bikes.index') }}">Bike Sharing</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 mb-4">
                    <h6>Contact</h6>
                    <p><i class="fas fa-phone me-2"></i>+265 990 179 811</p>
                    <p><i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw</p>
                    <p><i class="fas fa-clock me-2"></i>Support: 24/7</p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} Mzuni UNITRAS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
        
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>


<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mzuni UNITRAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar 
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Mzuni UNITRAS</a>
            <div class="ms-auto">
                <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-light">Register</a>
            </div>
        </div>
    </nav>

    <!-- Hero 
    <div class="bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4">Welcome to Mzuni UNITRAS</h1>
            <p class="lead">Your Campus Ride Is Just a Tap Away</p>
            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Get Started</a>
        </div>
    </div>

    <!-- Stats 
    <div class="container py-5">
        <div class="row text-center">
            <div class="col-md-4">
                <h2>{{ $stats['total_vehicles'] }}+</h2>
                <p>Registered Vehicles</p>
            </div>
            <div class="col-md-4">
                <h2>{{ $stats['total_users'] }}+</h2>
                <p>Active Users</p>
            </div>
            <div class="col-md-4">
                <h2>{{ $stats['completed_trips'] }}+</h2>
                <p>Completed Rides</p>
            </div>
        </div>
    </div>

    <!-- Features 
    <div class="bg-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-car fa-3x text-primary mb-3"></i>
                    <h4>Ride Sharing</h4>
                    <p>Share rides with fellow students and staff</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-bicycle fa-3x text-primary mb-3"></i>
                    <h4>Bike Sharing</h4>
                    <p>Rent bikes for your campus commute</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
                    <h4>Easy Payment</h4>
                    <p>Pay with mobile money or card</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer 
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} Mzuni UNITRAS. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> -->