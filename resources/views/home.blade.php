@extends('layouts.app')

@section('title', 'Home - Mzuni UNITRAS')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(rgba(0, 82, 155, 0.9), rgba(0, 82, 155, 0.8)), url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 0;
        margin-top: -70px;
    }
    
    .vehicle-card {
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .vehicle-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .badge-type {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 0.8rem;
    }
    
    .search-box {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Mzuni Unified Transport System</h1>
                <p class="lead mb-4">Find, book, and share rides across Mzuzu University. Safe, reliable, and affordable transportation for students and staff.</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Join Now
                    </a>
                    <a href="{{ route('search') }}" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-search me-2"></i>Find Rides
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Search -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="search-box">
            <h3 class="text-center mb-4">Find Your Ride</h3>
            <form action="{{ route('search') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <select class="form-select" name="from">
                            <option value="">Select Location</option>
                            <option value="Main Campus">Main Campus</option>
                            <option value="City Campus">City Campus</option>
                            <option value="Mzuzu City">Mzuzu City</option>
                            <option value="Mzimba">Mzimba</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <select class="form-select" name="to">
                            <option value="">Select Destination</option>
                            <option value="Main Campus">Main Campus</option>
                            <option value="City Campus">City Campus</option>
                            <option value="Mzuzu City">Mzuzu City</option>
                            <option value="Mzimba">Mzimba</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Vehicle Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            <option value="ride_share">Ride Share</option>
                            <option value="taxi">Taxi</option>
                            <option value="bus">Bus</option>
                            <option value="bike_share">Bike Share</option>
                        </select>
                    </div>
                    <div class="col-12 text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-search me-2"></i>Search Rides
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Vehicles -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2 class="text-center">Featured Vehicles</h2>
                <p class="text-center text-muted">Browse through our available transportation options</p>
            </div>
        </div>
        
        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs justify-content-center mb-4" id="vehicleTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                    <i class="fas fa-all me-2"></i>All Vehicles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="buses-tab" data-bs-toggle="tab" data-bs-target="#buses" type="button">
                    <i class="fas fa-bus me-2"></i>Buses
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cars-tab" data-bs-toggle="tab" data-bs-target="#cars" type="button">
                    <i class="fas fa-car me-2"></i>Cars & Taxis
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bikes-tab" data-bs-toggle="tab" data-bs-target="#bikes" type="button">
                    <i class="fas fa-bicycle me-2"></i>Bikes
                </button>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="vehicleTabsContent">
            <!-- All Vehicles Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="row">
                    @foreach($featuredVehicles as $advertisement)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        @include('partials.vehicle-card', ['advertisement' => $advertisement])
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Buses Tab -->
            <div class="tab-pane fade" id="buses" role="tabpanel">
                <div class="row">
                    @foreach($buses as $advertisement)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        @include('partials.vehicle-card', ['advertisement' => $advertisement])
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Cars Tab -->
            <div class="tab-pane fade" id="cars" role="tabpanel">
                <div class="row">
                    @foreach($cars as $advertisement)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        @include('partials.vehicle-card', ['advertisement' => $advertisement])
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Bikes Tab -->
            <div class="tab-pane fade" id="bikes" role="tabpanel">
                <div class="row">
                    @foreach($bikes as $advertisement)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        @include('partials.vehicle-card', ['advertisement' => $advertisement])
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('search') }}" class="btn btn-outline-primary">
                View All Vehicles <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">How Mzuni UNITRAS Works</h2>
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-plus fa-2x"></i>
                    </div>
                </div>
                <h4>1. Register</h4>
                <p>Create your account as a student, staff, or vehicle owner</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-search fa-2x"></i>
                    </div>
                </div>
                <h4>2. Search</h4>
                <p>Find available rides, taxis, buses, or bikes</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
                <h4>3. Book</h4>
                <p>Select your preferred vehicle and book your ride</p>
            </div>
            <div class="col-md-3 text-center mb-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-road fa-2x"></i>
                    </div>
                </div>
                <h4>4. Travel</h4>
                <p>Enjoy safe and reliable transportation</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">{{ $stats['total_vehicles'] }}+</h2>
                <p class="text-muted">Vehicles Registered</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">{{ $stats['total_users'] }}+</h2>
                <p class="text-muted">Happy Users</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">{{ $stats['completed_trips'] }}+</h2>
                <p class="text-muted">Completed Trips</p>
            </div>
            <div class="col-md-3 mb-4">
                <h2 class="text-primary fw-bold">{{ $stats['vehicle_owners'] }}+</h2>
                <p class="text-muted">Vehicle Owners</p>
            </div>
        </div>
    </div>
</section>
@endsection