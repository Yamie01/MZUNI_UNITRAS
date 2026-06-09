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
        /* Your existing styles remain unchanged */
        :root {
            --primary: #00529b;
            --primary-dark: #003f75;
            --secondary: #ff6b35;
            --light-bg: #e3f1f7;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--light-bg); overflow-x: hidden; }

        .navbar {
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(8px);
            box-shadow: 0 2px 18px rgba(0,0,0,0.05);
            padding: 0.8rem 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.65rem;
            background: linear-gradient(135deg, var(--primary), #0077be);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }
        .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
            border-radius: 40px;
        }
        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 40px;
        }
        .hero {
            background: linear-gradient(110deg, #f0f6fe 0%, #ffffff 100%);
            padding: 5rem 0 3rem;
            margin-top: 70px;
        }
        .hero-title {
            font-weight: 800;
            font-size: 3rem;
            line-height: 1.2;
        }
        .search-card {
            background: white;
            border-radius: 28px;
            padding: 1.8rem;
            box-shadow: 0 20px 35px -12px rgba(0,0,0,0.1);
        }
        .action-buttons-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .btn-offer, .btn-share {
            padding: 0.7rem 1.8rem;
            border-radius: 60px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
        }
        .btn-offer {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 10px rgba(0,82,155,0.2);
        }
        .btn-share {
            background: white;
            color: var(--primary-dark);
            border: 1px solid #cddfea;
        }
        .btn-offer:hover, .btn-share:hover {
            transform: translateY(-3px);
        }
        .service-tabs {
            background: white;
            border-radius: 60px;
            display: inline-flex;
            padding: 0.4rem;
            border: 1px solid #e2edf2;
            margin-bottom: 2rem;
        }
        .tab-btn {
            background: transparent;
            border: none;
            padding: 0.7rem 2rem;
            border-radius: 60px;
            font-weight: 600;
            color: #4a6272;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tab-btn.active {
            background: var(--primary);
            color: white;
        }
        .ride-card, .bike-card {
            background: white;
            border-radius: 24px;
            transition: 0.25s;
            border: 1px solid #eef2f8;
            height: 100%;
            cursor: pointer;
            overflow: hidden;
        }
        .ride-card:hover, .bike-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(0,82,155,0.12);
        }
        .card-img {
            height: 160px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9f0f5;
        }
        .price {
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--primary);
        }
        .book-btn {
            background: transparent;
            border: 1px solid #cddfea;
            border-radius: 40px;
            padding: 0.4rem;
            font-weight: 500;
            transition: 0.2s;
        }
        .book-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .stat-container {
            background: linear-gradient(120deg, var(--primary), #0c4e7a);
            border-radius: 48px;
        }
        .info-card {
            background: white;
            border-radius: 20px;
            padding: 1.2rem;
            margin-top: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        #map {
            height: 300px;
            border-radius: 16px;
            margin: 15px 0;
            border: 1px solid #dee2e6;
            z-index: 1;
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
        .location-input-group input {
            flex: 1;
        }
        .btn-geolocate {
            background: #60a4e7;
            border: none;
            border-radius: 40px;
            padding: 6px 12px;
        }
        footer {
            background: #c7a0e7;
            color: #3fdd7c;
        }
        @media (max-width: 768px) {
            .hero-title { font-size: 2rem; }
            .tab-btn { padding: 0.5rem 1.2rem; font-size: 0.85rem; }
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
                <h1 class="hero-title">Your Campus Ride, <span style="background: linear-gradient(135deg, var(--primary), var(--secondary)); background-clip: text; -webkit-background-clip: text; color: transparent;">Just a Tap Away</span></h1>
                <p class="lead mt-3">Safe carpool & bike sharing for students, staff, and locals. Browse freely – book only when you're ready.</p>
                <div class="action-buttons-group mt-4">
                    <button class="btn-offer" id="heroOfferBtn"><i class="fas fa-plus-circle"></i> Offer a ride</button>
                    <button class="btn-share" id="heroShareBtn"><i class="fas fa-share-alt"></i> Share a ride</button>
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
                    <div id="map"></div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">From (pickup)</label>
                        <div class="location-input-group">
                            <input type="text" id="searchFrom" class="form-control" list="locationList" placeholder="Type or select location" autocomplete="off">
                            <button id="geolocateBtn" class="btn-geolocate" title="Use my current location"><i class="fas fa-location-dot"></i></button>
                        </div>
                        <datalist id="locationList">
                            @foreach($locations as $location)
                                <option value="{{ $location }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div class="mb-3" id="toFieldWrapper">
                        <label class="form-label text-muted small">To (destination)</label>
                        <input type="text" id="searchTo" class="form-control" list="locationList" placeholder="Type or select destination" autocomplete="off">
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

<!-- Services Section (with data-ride-id and data-bike-id) -->
<section id="services" class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div class="service-tabs">
                <button class="tab-btn active" id="tabCarpool" data-tab="carpool">
                    <i class="fas fa-car-side"></i> Carpool (Ride sharing)
                </button>
                <button class="tab-btn" id="tabBike" data-tab="bike">
                    <i class="fas fa-bicycle"></i> Bike sharing
                </button>
            </div>
            <div class="action-buttons-group">
                <button class="btn-offer" id="tabOfferBtn"><i class="fas fa-plus-circle"></i> Offer a ride</button>
                <button class="btn-share" id="tabShareBtn"><i class="fas fa-share-alt"></i> Share a ride</button>
            </div>
        </div>

        <!-- Carpool Panel (rides) -->
        <div id="carpoolPanel">
            <div class="row g-4" id="ridesList">
                @forelse($availableVehicles as $ride)
                    <div class="col-lg-4 col-md-6 ride-item" 
                         data-ride-id="{{ $ride->id }}"
                         data-from="{{ strtolower($ride->from_location) }}" 
                         data-to="{{ strtolower($ride->to_location) }}" 
                         data-date="{{ \Carbon\Carbon::parse($ride->departure_time)->format('Y-m-d') }}"
                         data-price="{{ $ride->price }}">
                        <div class="ride-card">
                            <div class="card-img"><i class="fas fa-car-side fa-3x text-primary"></i></div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-primary-light text-primary">{{ ucfirst(str_replace('_', ' ', $ride->ad_type)) }}</span>
                                    <span class="price">MWK {{ number_format($ride->price, 0) }}</span>
                                </div>
                                <h5 class="fw-bold mt-2">{{ $ride->from_location }} → {{ $ride->to_location }}</h5>
                                <div class="text-muted small">
                                    <i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M Y, H:i') }}
                                    <span class="ms-2"><i class="fas fa-users"></i> {{ $ride->available_seats }} seats</span>
                                </div>
                                <div class="mt-2 small text-warning">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i> 4.8 (120 reviews)
                                </div>
                                <div class="d-grid mt-3">
                                    <button class="book-btn book-action" data-type="ride">Book ride <i class="fas fa-arrow-right ms-1"></i></button>
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
                @forelse($availableBikes as $bike)
                    <div class="col-lg-3 col-md-6 bike-item" 
                         data-bike-id="{{ $bike->id }}"
                         data-location="{{ strtolower($bike->pickup_location ?? $bike->location ?? 'campus') }}" 
                         data-type="{{ strtolower($bike->type) }}">
                        <div class="bike-card">
                            <div class="card-img"><i class="fas fa-bicycle fa-3x text-primary"></i></div>
                            <div class="p-3">
                                <h5 class="fw-bold">{{ $bike->brand }} {{ $bike->model }}</h5>
                                <div class="text-muted small">{{ ucfirst($bike->type) }} Bike</div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span>Hourly:</span><strong>MWK {{ number_format($bike->price_per_hour, 0) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Daily:</span><strong>MWK {{ number_format($bike->price_per_day, 0) }}</strong>
                                </div>
                                <span class="badge bg-success mb-2"><i class="fas fa-check-circle"></i> Available now</span>
                                <div class="d-grid">
                                    <button class="book-btn book-action" data-type="bike">Rent now <i class="fas fa-arrow-right ms-1"></i></button>
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
                    <p class="small text-muted">MZUNI Main Gate → City Centre (MWK 3,000)<br>Luwinga → MZUNI Library (MWK 1,800)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="fas fa-shield-alt fa-2x text-primary mb-2"></i>
                    <h6>Safety First</h6>
                    <p class="small text-muted">All drivers/bikes verified. 24/7 support. Live tracking on every ride.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <i class="fas fa-wallet fa-2x text-primary mb-2"></i>
                    <h6>Estimated Savings</h6>
                    <p class="small text-muted">Carpool saves up to 70% vs taxi. Bike rentals from MWK 350/hour.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About & Stats -->
<section id="about" class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-primary-light text-primary mb-2 px-3 py-2 rounded-pill"><i class="fas fa-leaf me-1"></i> Eco-friendly</span>
                <h2 class="fw-bold">Unified Transport for Mzuzu University</h2>
                <p class="lead text-muted">UNITRAS connects Staff(vehicle owners),and passengers in a seamless, affordable and sustainable ecosystem.</p>
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
                    <p class="mb-0 text-center"><i class="fas fa-map-marked-alt me-1"></i> Covering MZUNI, Luwinga, Town, and Chibavi</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5">
    <div class="container">
        <div class="bg-gradient rounded-4 p-5 text-center" style="background: linear-gradient(125deg, #eef4fc, #ffffff); border: 1px solid #dce7f0;">
            <h3 class="fw-bold">Ready to share the journey?</h3>
            <p class="mb-4">Join Mzuni UNITRAS today — offer a seat, rent a bike.</p>
            <button class="btn btn-primary rounded-pill px-5" data-bs-toggle="modal" data-bs-target="#loginModal"><i class="fas fa-user-plus"></i> Create free account</button>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer pt-5 pb-3">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4"><h5><i class="fas fa-bus me-2"></i>Mzuni UNITRAS</h5><p class="text-muted small">Mzuzu University, Luwinga</p></div>
            <div class="col-md-2 mb-4"><h6>Quick</h6><ul class="list-unstyled small"><li><a href="#services" class="text-muted text-decoration-none">Rides</a></li><li><a href="#services" class="text-muted text-decoration-none">Bikes</a></li><li><a href="#about" class="text-muted text-decoration-none">About</a></li></ul></div>
            <div class="col-md-3 mb-4"><h6>Contact</h6><p class="text-muted small"><i class="fas fa-phone me-2"></i>+265 990 179 811<br><i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw</p></div>
            <div class="col-md-3 mb-4"><h6>Social</h6><div class="d-flex gap-3"><a href="#" class="text-muted"><i class="fab fa-facebook-f"></i></a><a href="#" class="text-muted"><i class="fab fa-twitter"></i></a><a href="#" class="text-muted"><i class="fab fa-instagram"></i></a></div></div>
        </div>
        <hr class="opacity-25"><div class="text-center text-muted small">&copy; {{ date('Y') }} Mzuni UNITRAS — browse freely, book after login.</div>
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
    // ---------- LEAFET MAP (unchanged) ----------
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

    async function forwardGeocode(address, callback) {
        if (!address.trim()) return callback(null, null);
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`);
            const data = await res.json();
            if (data.length > 0) {
                callback(parseFloat(data[0].lat), parseFloat(data[0].lon));
            } else {
                callback(null, null);
            }
        } catch {
            callback(null, null);
        }
    }

    function setFromLocation(lat, lng, name) {
        fromLatLng = { lat, lng };
        if (fromMarker) map.removeLayer(fromMarker);
        fromMarker = L.marker([lat, lng]).addTo(map).bindPopup('Pickup').openPopup();
        document.getElementById('searchFrom').value = name;
        drawPolyline();
        setTimeout(() => filterBySearch(), 100);
    }

    function setToLocation(lat, lng, name) {
        toLatLng = { lat, lng };
        if (toMarker) map.removeLayer(toMarker);
        toMarker = L.marker([lat, lng]).addTo(map).bindPopup('Destination').openPopup();
        document.getElementById('searchTo').value = name;
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

    const fromInput = document.getElementById('searchFrom');
    const toInput = document.getElementById('searchTo');

    function onAddressSelect(input, setFunc) {
        const address = input.value.trim();
        if (!address) return;
        forwardGeocode(address, (lat, lng) => {
            if (lat && lng) {
                setFunc(lat, lng, address);
                map.setView([lat, lng], 15);
            }
        });
    }

    fromInput.addEventListener('change', () => onAddressSelect(fromInput, setFromLocation));
    toInput.addEventListener('change', () => onAddressSelect(toInput, setToLocation));

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

    function filterRides() {
        const fromVal = fromInput.value.trim().toLowerCase();
        const toVal = toInput.value.trim().toLowerCase();
        const dateVal = searchDate.value;
        const rideItems = document.querySelectorAll('#ridesList .ride-item');
        let visibleCount = 0;
        rideItems.forEach(item => {
            const fromAttr = (item.getAttribute('data-from') || '').toLowerCase();
            const toAttr = (item.getAttribute('data-to') || '').toLowerCase();
            const itemDate = item.getAttribute('data-date') || '';
            let show = true;
            if (fromVal && !fromAttr.includes(fromVal)) show = false;
            if (toVal && !toAttr.includes(toVal)) show = false;
            if (dateVal && itemDate !== dateVal) show = false;
            item.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });
        showNoResultsMessage('ridesList', visibleCount, 'rides');
    }

    function filterBikes() {
        const locVal = fromInput.value.trim().toLowerCase();
        const typeVal = bikeTypeFilter.value.toLowerCase();
        const bikeItems = document.querySelectorAll('#bikesList .bike-item');
        let visibleCount = 0;
        bikeItems.forEach(item => {
            const locationAttr = (item.getAttribute('data-location') || '').toLowerCase();
            const bikeTypeAttr = (item.getAttribute('data-type') || '').toLowerCase();
            let show = true;
            if (locVal && !locationAttr.includes(locVal)) show = false;
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

    fromInput.addEventListener('change', filterBySearch);
    toInput.addEventListener('change', () => { if (carpoolPanel.style.display !== 'none') filterRides(); });
    searchDate.addEventListener('change', () => { if (carpoolPanel.style.display !== 'none') filterRides(); });
    bikeTypeFilter.addEventListener('change', () => { if (bikePanel.style.display !== 'none') filterBikes(); });

    document.getElementById('navSearchIcon').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('searchCard').scrollIntoView({ behavior: 'smooth' });
    });

    // ---------- GUEST ACTION HANDLER (REDIRECT TO LOGIN WITH RETURN URL) ----------
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    function redirectToLoginWithReturn(targetUrl) {
    if (targetUrl) {
        // Direct redirect with query parameter (no sessionStorage)
        window.location.href = "{{ route('login') }}?redirect_to=" + encodeURIComponent(targetUrl);
    } else {
        loginModal.show();
    }
    }

    // For "Book ride" and "Rent now" buttons, redirect directly to login (no modal)
    document.querySelectorAll('.book-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-type');
            let targetUrl = '';
            if (type === 'ride') {
                const rideId = this.closest('.ride-item')?.dataset?.rideId;
                if (rideId) targetUrl = "/book/" + rideId;
            } else if (type === 'bike') {
                const bikeId = this.closest('.bike-item')?.dataset?.bikeId;
                if (bikeId) targetUrl = "/bikes/" + bikeId + "/rent";
            }
            redirectToLoginWithReturn(targetUrl);
        });
    });

    // For "Offer a ride" and "Share a ride" buttons, show the login modal
    document.querySelectorAll('#heroOfferBtn, #heroShareBtn, #tabOfferBtn, #tabShareBtn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            loginModal.show();
        });
    });

    // For entire card clicks: same logic
    document.querySelectorAll('.ride-card, .bike-card').forEach(card => {
        card.addEventListener('click', (e) => {
            if (!e.target.closest('.book-action')) {
                const rideItem = card.closest('.ride-item');
                if (rideItem) {
                    const rideId = rideItem?.dataset?.rideId;
                    if (rideId) redirectToLoginWithReturn("/book/" + rideId);
                } else {
                    const bikeId = card.closest('.bike-item')?.dataset?.bikeId;
                    if (bikeId) redirectToLoginWithReturn("/bikes/" + bikeId + "/rent");
                }
            }
        });
    });

    @auth
    // For logged‑in users, redirect directly (no modal, no login redirect)
    document.querySelectorAll('.book-action').forEach(btn => {
        btn.removeEventListener('click', redirectToLoginWithReturn);
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.getAttribute('data-type');
            if (type === 'ride') {
                const rideId = this.closest('.ride-item')?.dataset?.rideId;
                if (rideId) window.location.href = "/book/" + rideId;
            } else if (type === 'bike') {
                const bikeId = this.closest('.bike-item')?.dataset?.bikeId;
                if (bikeId) window.location.href = "/bikes/" + bikeId + "/rent";
            }
        });
    });
    document.querySelectorAll('.ride-card, .bike-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.book-action')) {
                const rideId = this.closest('.ride-item')?.dataset?.rideId;
                if (rideId) window.location.href = "/book/" + rideId;
                else {
                    const bikeId = this.closest('.bike-item')?.dataset?.bikeId;
                    if (bikeId) window.location.href = "/bikes/" + bikeId + "/rent";
                }
            }
        });
    });
    @endauth
</script>
</body>
</html>