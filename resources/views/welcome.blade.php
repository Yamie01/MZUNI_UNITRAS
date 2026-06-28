<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mzuni UNITRAS</title>

    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS + JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --primary: #0D6EFD;
            --secondary: #198754;
            --accent: #FD7E14;
            --dark: #1E293B;
            --light: #F8F9FA;
            --text: #212529;
            font-size: 18px;
        }

        * { font-family: 'Inter', sans-serif; }

        body {
            font-size: 1.05rem;
            line-height: 1.7;
            background: linear-gradient(
                rgba(13,110,253,0.75),
                rgba(25,135,84,0.65)
            ),
            url('https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=1200') no-repeat center center fixed;
            background-size: cover;
            color: white;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            background: var(--dark) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
            padding: 0.9rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.6rem;
            color: white !important;
        }
        .navbar .nav-link {
            color: rgba(255,255,255,0.85) !important;
            font-weight: 500;
        }
        .navbar .nav-link:hover { color: var(--accent) !important; }

        /* BUTTONS */
        .btn-primary {
            background: var(--primary);
            border: none;
        }
        .btn-primary:hover { background: #084298; }
        .btn-outline-primary {
            border-color: white;
            color: white;
        }
        .btn-outline-primary:hover {
            background: white;
            color: var(--primary);
        }

        /* HERO */
        .hero {
            padding: 6rem 0 3rem;
            min-height: 600px;
            display: flex;
            align-items: center;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
        }
        .lead {
            font-size: 1.3rem;
            opacity: 0.95;
        }

        /* ACTION BUTTONS */
        .btn-offer {
            background: var(--secondary);
            color: white;
            border-radius: 50px;
            padding: 0.9rem 2rem;
            border: none;
            display: inline-block;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-share {
            background: transparent;
            border: 2px solid white;
            color: white;
            border-radius: 50px;
            padding: 0.9rem 2rem;
            display: inline-block;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-offer:hover, .btn-share:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }
        .btn-offer.accent {
            background: var(--accent);
        }
        .btn-offer.accent:hover {
            background: #e66a00;
        }

        /* SEARCH CARD */
        .search-card {
            background: rgba(255,255,255,0.95);
            border-radius: 25px;
            padding: 2rem;
            color: var(--text);
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }

        /* SERVICE SECTION */
        .services-section,
        .about-section,
        .cta-section {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem 0;
        }

        /* TAB BUTTONS */
        .tab-btn {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 0.7rem 2rem;
            border-radius: 50px;
        }
        .tab-btn.active {
            background: white;
            color: var(--primary);
        }

        /* CARDS */
        .ride-card, .bike-card {
            background: white;
            color: var(--text);
            border-radius: 20px;
            transition: 0.3s ease;
            overflow: hidden;
        }
        .ride-card:hover, .bike-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(13,110,253,0.25);
        }
        .price { color: var(--primary); font-weight: 800; }

        /* INFO CARDS */
        .info-card {
            background: white;
            color: var(--text);
            border-left: 5px solid var(--primary);
            border-radius: 15px;
        }

        /* STAT BOX */
        .stat-container {
            background: var(--primary);
            border-radius: 30px;
        }

        /* FOOTER */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.8);
        }

        /* MAP */
        #map {
            height: 300px;
            border-radius: 15px;
            border: 1px solid #ccc;
        }
        .map-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .btn-map {
            background: #f1f3f5;
            border: none;
            border-radius: 40px;
            padding: 5px 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .location-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .location-input-group select {
            flex: 1;
        }
        .btn-geolocate {
            background: #60a4e7;
            border: none;
            border-radius: 40px;
            padding: 6px 12px;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .tab-btn { padding: 0.5rem 1.2rem; font-size: 0.85rem; }
        }

        /* Rented bike overlay */
        .bike-card.rented {
            opacity: 0.6;
            pointer-events: none;
        }
        .bike-card.rented .card-img {
            filter: grayscale(1);
        }
        
        /* Action button styles */
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
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni UNITRAS" height="40" class="d-inline-block align-middle me-2">
            <span class="fw-bold">Mzuni UNITRAS</span>
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
                    <li class="nav-item"><a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-4">Login</a></li>
                    <li class="nav-item"><a href="{{ route('register') }}" class="btn btn-primary btn-sm px-4">Sign Up</a></li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">{{ Auth::user()->name }}</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-lg-6">
                <div class="badge bg-light text-primary rounded-pill mb-3"><i class="fas fa-shield-alt me-1"></i> Trusted by Mzuzu Community</div>
                <h1 class="hero-title">Your Campus Ride, <span style="background: linear-gradient(135deg, #fff, #ffd966); background-clip: text; -webkit-background-clip: text; color: transparent;">Just a Tap Away</span></h1>
                <p class="lead mt-3">Safe carpool & bike sharing for students, staff, and locals. Browse freely – book only when you're ready.</p>
                <div class="action-btn-group mt-4">
                    <a href="{{ route('user.bikes.index') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-bicycle me-2"></i> Pick a Bike
                    </a>
                    <a href="{{ route('search') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-car me-2"></i> Pick a Ride
                    </a>
                    <a href="{{ route('offer.ride') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="search-card" id="searchCard">
                    <h5 class="mb-3"><i class="fas fa-map-marked-alt text-primary"></i> Pick your route on the map</h5>
                    <div class="map-buttons">
                        <button id="setFromBtn" class="btn-map"><i class="fas fa-map-pin"></i> Set as "From"</button>
                        <button id="setToBtn" class="btn-map"><i class="fas fa-flag-checkered"></i> Set as "To"</button>
                        <button id="clearMarkersBtn" class="btn-map"><i class="fas fa-eraser"></i> Clear</button>
                    </div>
                    <div id="map" style="height:300px; border-radius:15px; background:#e9ecef;"></div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">From (pickup)</label>
                        <div class="location-input-group">
                            <select id="searchFrom" class="form-select">
                                <option value="">Select pickup location</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}" {{ request('from_location_id') == $loc->id ? 'selected' : '' }}>
                                        {{ $loc->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button id="geolocateBtn" class="btn-geolocate" title="Use my current location"><i class="fas fa-location-dot"></i></button>
                        </div>
                    </div>
                    <div class="mb-3" id="toFieldWrapper">
                        <label class="form-label text-muted small">To (destination)</label>
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
                        <label class="form-label text-muted small">Bike type</label>
                        <select id="bikeTypeFilter" class="form-select">
                            <option value="">All types</option>
                            <option value="mountain">Mountain</option>
                            <option value="city">City</option>
                            <option value="hybrid">Hybrid</option>
                            <option value="electric">Electric</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">Date</label>
                        <input type="date" id="searchDate" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <button class="btn btn-primary w-100 py-2" id="searchBtn"><i class="fas fa-arrow-right me-2"></i>Search rides & bikes</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-5">
    <div class="container">
        <div class="services-section">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div class="service-tabs">
                    <button class="tab-btn active" id="tabCarpool" data-tab="carpool">
                        <i class="fas fa-car-side"></i> Carpool (Ride sharing)
                    </button>
                    <button class="tab-btn" id="tabBike" data-tab="bike">
                        <i class="fas fa-bicycle"></i> Bike sharing
                    </button>
                </div>
                <a href="{{ route('offer.ride') }}" class="btn btn-warning">
                    <i class="fas fa-plus-circle me-2"></i> Offer a Ride
                </a>
            </div>

            <!-- Carpool Panel -->
            <div id="carpoolPanel">
                <div class="row g-4" id="ridesList">
                    @forelse($availableVehicles as $ride)
                        @php
                            $fromName = $ride->fromLocation->name ?? $ride->from_location ?? 'N/A';
                            $toName   = $ride->toLocation->name ?? $ride->to_location ?? 'N/A';
                        @endphp
                        <div class="col-lg-4 col-md-6 ride-item" 
                             data-ride-id="{{ $ride->id }}"
                             data-from-id="{{ $ride->from_location_id ?? '' }}"
                             data-to-id="{{ $ride->to_location_id ?? '' }}"
                             data-date="{{ \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d') }}"
                             data-price="{{ $ride->price }}">
                            <div class="ride-card">
                                <div class="card-img p-3 text-center bg-light">
                                    <i class="fas fa-car-side fa-4x text-primary"></i>
                                </div>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between">
                                        <span class="badge bg-primary-light text-primary">{{ ucfirst(str_replace('_', ' ', $ride->ad_type)) }}</span>
                                        <span class="price">MWK {{ number_format($ride->price, 0) }}</span>
                                    </div>
                                    <h5 class="fw-bold mt-2">{{ $fromName }} → {{ $toName }}</h5>
                                    <div class="text-muted small">
                                        <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M Y, H:i') }}
                                        <span class="ms-2"><i class="fas fa-users"></i> {{ $ride->available_seats }} seats</span>
                                    </div>
                                    <div class="d-grid mt-3">
                                        <a href="{{ route('user.bookings.create', $ride) }}" class="btn btn-primary book-action" data-type="ride">
                                            Hop In <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5"><i class="fas fa-car fa-3x text-muted mb-3"></i><h5>No rides available</h5></div>
                    @endforelse
                </div>
            </div>

            <!-- Bike Panel -->
            <div id="bikePanel" style="display: none;">
                <div class="row g-4" id="bikesList">
                    @php
                        $availableBikes = $availableBikes->filter(function($bike) {
                            return $bike->status === 'available';
                        });
                    @endphp
                    @forelse($availableBikes as $bike)
                        <div class="col-lg-3 col-md-6 bike-item" 
                             data-bike-id="{{ $bike->id }}"
                             data-location-id="{{ $bike->location_id ?? '' }}"
                             data-type="{{ strtolower($bike->type) }}">
                            <div class="bike-card">
                                <div class="card-img p-3 text-center bg-light">
                                    <i class="fas fa-bicycle fa-4x text-success"></i>
                                </div>
                                <div class="p-3">
                                    <h5 class="fw-bold">{{ $bike->brand }} {{ $bike->model }}</h5>
                                    <div class="text-muted small">{{ ucfirst($bike->type) }} Bike</div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <span>Rate:</span><strong>MWK 2/min</strong>
                                    </div>
                                    <span class="badge bg-success mb-2"><i class="fas fa-check-circle"></i> Available now</span>
                                    <div class="d-grid">
                                        <a href="{{ route('user.bikes.rent', $bike) }}" class="btn btn-primary book-action" data-type="bike">
                                            Rent now <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5"><i class="fas fa-bicycle fa-3x text-muted mb-3"></i><h5>No bikes available</h5></div>
                    @endforelse
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-4">
                    <div class="info-card">
                        <i class="fas fa-chart-line fa-2x text-primary mb-2"></i>
                        <h6>Popular Routes</h6>
                        <p class="small text-muted">MZUNI Main Gate → Mzuzu Town (MWK 2,500)<br>Luwinga → MZUNI Library (MWK 1,000)</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                        <h6>Safety First</h6>
                        <p class="small text-muted">All Vehicles/bikes verified. 24/7 support. Live tracking on every ride and Bikes.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card">
                        <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                        <h6>Estimated Savings</h6>
                        <p class="small text-muted">Carpool/Ride Share saves up to 70%. Bike rental MWK 2/minute.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About & Stats -->
<section id="about" class="py-5">
    <div class="container">
        <div class="about-section">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge bg-light text-primary mb-2 px-3 py-2 rounded-pill"><i class="fas fa-leaf me-1"></i> Eco-friendly</span>
                    <h2 class="fw-bold">Unified Transport for Mzuzu University</h2>
                    <p class="lead">UNITRAS connects Staff (vehicle owners) and passengers in a seamless, affordable and sustainable ecosystem.</p>
                    <button class="btn btn-primary rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#loginModal">Join community</button>
                </div>
                <div class="col-lg-6">
                    <div class="stat-container text-white p-4 rounded-4 shadow">
                        <div class="row text-center">
                            <div class="col-4"><h2 class="fw-bold">{{ number_format($stats['total_vehicles'] ?? 0) }}+</h2><p>Vehicles</p></div>
                            <div class="col-4"><h2 class="fw-bold">{{ number_format($stats['total_users'] ?? 0) }}+</h2><p>Users</p></div>
                            <div class="col-4"><h2 class="fw-bold">{{ number_format($stats['completed_trips'] ?? 0) }}+</h2><p>Trips</p></div>
                        </div>
                        <hr class="bg-white opacity-25">
                        <p class="mb-0 text-center"><i class="fas fa-map-marked-alt me-1"></i> Covering Mzuzu University main campus, Luwinga, Dunduzu Campus, Mzuzu Town and Chibavi and surrounding areas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5">
    <div class="container">
        <div class="cta-section text-center p-5 rounded-4" style="background: rgba(0,0,0,0.2); backdrop-filter: blur(4px);">
            <h3 class="fw-bold">Ready to share the journey?</h3>
            <p class="mb-4">Join Mzuni UNITRAS today — offer a seat, rent a bike.</p>
            <button class="btn btn-primary rounded-pill px-5" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-user-plus"></i> Create free account</button>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="pt-5 pb-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4"><h5><i class="fas fa-bus me-2"></i>Mzuni UNITRAS</h5><p class="text-muted small">Mzuzu University, Luwinga</p></div>
            <div class="col-md-2 mb-4"><h6>Quick</h6><ul class="list-unstyled small"><li><a href="#services">Rides</a></li><li><a href="#services">Bikes</a></li><li><a href="#about">About</a></li></ul></div>
            <div class="col-md-3 mb-4"><h6>Contact</h6><p class="text-muted small"><i class="fas fa-phone me-2"></i>+265 990 179 811<br><i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw</p></div>
            <div class="col-md-3 mb-4"><h6>Social</h6><div class="d-flex gap-3"><a href="#"><i class="fab fa-facebook-f"></i></a><a href="#"><i class="fab fa-twitter"></i></a><a href="#"><i class="fab fa-instagram"></i></a></div></div>
        </div>
        <hr class="opacity-25">
        <div class="text-center text-muted small">&copy; {{ date('Y') }} Mzuni UNITRAS — browse freely, book after login.</div>
    </div>
</footer>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;"><i class="fas fa-lock fa-2x text-primary"></i></div>
                <h4 class="fw-bold">Sign in to continue</h4>
                <p class="text-muted">You're one step away from booking a ride, renting a bike, or offering a seat.<br>Create an account or log in.</p>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill py-2">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary rounded-pill">Create new account</a>
                </div>
                <p class="small text-muted mt-3">⚡ Browse everything, only register to book/offer.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ---------- LOCATION MAPPING ----------
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

    // ---------- LEAFET MAP ----------
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
                color: '#00529b',
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

    document.getElementById('setFromBtn').onclick = () => { mapClickMode = 'from'; alert('Click on the map to set pickup location'); };
    document.getElementById('setToBtn').onclick = () => { mapClickMode = 'to'; alert('Click on the map to set destination'); };
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

    // ---------- TAB SWITCHING ----------
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

    // ---------- FILTERING ----------
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
                msg.innerHTML = `<i class="fas fa-search fa-2x text-muted mb-2"></i><p>No ${type} match your search. Try different filters.</p>`;
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
    bikeTypeFilter.addEventListener('change', () => { if (bikePanel.style.display !== 'none') filterBikes(); });

    document.getElementById('navSearchIcon').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('searchCard').scrollIntoView({ behavior: 'smooth' });
    });

    // ---------- GUEST / AUTH ACTION HANDLING ----------
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));

    function redirectToLoginWithReturn(targetUrl) {
        if (targetUrl) {
            window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent(targetUrl);
        } else {
            loginModal.show();
        }
    }

    // Initial filter on page load
    setTimeout(filterBySearch, 500);
</script>
</body>
</html>