@extends('layouts.app')

@section('title', 'Dashboard - Mzuni UNITRAS')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.2s;
        height: 100%;
        border: 1px solid #eef2f8;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -10px rgba(0,82,155,0.15); }
    .stat-icon { width: 48px; height: 48px; background: #00529b; border-radius: 30px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; }
    
    .ride-card, .bike-card {
        background: white;
        border-radius: 20px;
        transition: all 0.2s;
        border: 1px solid #eef2f8;
        cursor: pointer;
        overflow: hidden;
        height: 100%;
    }
    .ride-card:hover, .bike-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -12px rgba(0,82,155,0.2);
        border-color: #00529b;
    }
    .card-img-icon {
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #eef4fc, #dce8f2);
    }
    .price-tag { font-weight: 800; font-size: 1.2rem; color: #00529b; }
    .badge-available { background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; }
    
    .recent-item {
        background: white;
        border-radius: 16px;
        padding: 0.8rem;
        margin-bottom: 0.8rem;
        border-left: 4px solid #00529b;
        transition: 0.2s;
    }
    
    .dashboard-sidebar {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 20px 0;
    }
    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu li { margin-bottom: 5px; }
    .sidebar-menu a {
        display: flex;
        align-items: center;
        padding: 10px 20px;
        color: #4a5568;
        text-decoration: none;
        transition: all 0.2s;
        border-left: 3px solid transparent;
    }
    .sidebar-menu a i { width: 25px; margin-right: 10px; }
    .sidebar-menu a:hover { background: #f7fafc; color: #00529b; }
    .sidebar-menu a.active { background: #ebf8ff; color: #00529b; border-left-color: #00529b; }
    
    .dashboard-content { background: white; border-radius: 16px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 25px; min-height: 500px; }
    
    @media (max-width: 768px) {
        .dashboard-sidebar { margin-bottom: 20px; }
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="dashboard-sidebar">
                <div class="text-center mb-3 pt-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x"></i>
                    </div>
                    <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
                    <small class="text-muted">{{ ucfirst(Auth::user()->user_type) }}</small>
                </div>
                <hr>
                <ul class="sidebar-menu">
                    <li><a href="#" class="menu-item active" data-section="overview"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
                    <li><a href="#" class="menu-item" data-section="available-rides"><i class="fas fa-car"></i> Available Rides</a></li>
                    <li><a href="#" class="menu-item" data-section="available-bikes"><i class="fas fa-bicycle"></i> Available Bikes</a></li>
                    <li><a href="#" class="menu-item" data-section="bookings"><i class="fas fa-calendar-check"></i> My Bookings</a></li>
                    <li><a href="#" class="menu-item" data-section="bike-rentals"><i class="fas fa-history"></i> Bike Rentals</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="menu-item"><i class="fas fa-user-circle"></i> My Profile</a></li>
                </ul>
                <hr>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('subscription.index') }}" class="menu-item"><i class="fas fa-ticket-alt"></i> Subscription</a></li>
                    <li><a href="{{ route('search') }}" class="menu-item"><i class="fas fa-search"></i> Search Rides</a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="dashboard-content" id="dashboardContent">
                
                <!-- ========== OVERVIEW SECTION ========== -->
                <div id="overviewSection">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold mb-0">Welcome back, {{ Auth::user()->name }}! 👋</h4>
                            <p class="text-muted">Here's what's happening with your travel activity.</p>
                        </div>
                        <a href="{{ route('search') }}" class="btn btn-primary rounded-pill px-4"><i class="fas fa-search me-2"></i>Find a ride</a>
                    </div>

                    <!-- Quick Actions -->
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <a href="{{ route('search') }}" class="btn action-btn btn-primary"><i class="fas fa-car me-2"></i>Book a ride</a>
                        <a href="{{ route('user.bikes.index') }}" class="btn action-btn btn-outline-primary"><i class="fas fa-bicycle me-2"></i>Rent a bike</a>
                        <a href="{{ route('subscription.index') }}" class="btn action-btn btn-outline-primary"><i class="fas fa-ticket-alt me-2"></i>Subscription</a>
                        <a href="{{ route('profile.edit') }}" class="btn action-btn btn-outline-secondary"><i class="fas fa-user-edit me-2"></i>Edit profile</a>
                    </div>

                    <!-- Available Rides Preview -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-car text-primary me-2"></i>Available Rides – One Click to Book</h5>
                        <div class="row g-3">
                            @forelse(($availableRides ?? [])->take(3) as $ride)
                            <div class="col-md-4 col-sm-6">
                                <div class="ride-card" onclick="window.location.href='{{ route('user.bookings.create', $ride) }}'">
                                    <div class="card-img-icon">
                                        <i class="fas fa-car-side fa-3x text-primary"></i>
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between">
                                            <strong class="small">{{ Str::limit($ride->from_location, 15) }}</strong>
                                            <i class="fas fa-arrow-right text-muted fa-xs"></i>
                                            <strong class="small">{{ Str::limit($ride->to_location, 15) }}</strong>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M H:i') }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="price-tag">MWK {{ number_format($ride->price, 0) }}</span>
                                            <span class="badge-available"><i class="fas fa-users me-1"></i>{{ $ride->available_seats }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><div class="alert alert-info">No rides available at the moment. <a href="{{ route('search') }}">Browse rides →</a></div></div>
                            @endforelse
                        </div>
                        @if(($availableRides ?? [])->count() > 3)
                        <div class="text-center mt-2">
                            <a href="#" onclick="document.querySelector('[data-section=\'available-rides\']').click(); return false;" class="text-primary small">View all {{ $availableRides->count() }} rides →</a>
                        </div>
                        @endif
                    </div>

                    <!-- Available Bikes Preview -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-bicycle text-primary me-2"></i>Available Bikes – One Click to Rent</h5>
                        <div class="row g-3">
                            @forelse(($availableBikes ?? [])->take(4) as $bike)
                            <div class="col-md-3 col-sm-6">
                                <div class="bike-card" onclick="window.location.href='{{ route('user.bikes.rent', $bike) }}'">
                                    <div class="card-img-icon">
                                        <i class="fas fa-bicycle fa-3x text-success"></i>
                                    </div>
                                    <div class="p-2 text-center">
                                        <h6 class="fw-bold mb-0">{{ $bike->brand }}</h6>
                                        <small class="text-muted">{{ $bike->model }}</small>
                                        <div class="mt-1">
                                            <span class="price-tag">MWK {{ number_format($bike->price_per_hour, 0) }}/hr</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12"><div class="alert alert-info">No bikes available at the moment. <a href="{{ route('user.bikes.index') }}">Browse bikes →</a></div></div>
                            @endforelse
                        </div>
                        @if(($availableBikes ?? [])->count() > 4)
                        <div class="text-center mt-2">
                            <a href="#" onclick="document.querySelector('[data-section=\'available-bikes\']').click(); return false;" class="text-primary small">View all {{ $availableBikes->count() }} bikes →</a>
                        </div>
                        @endif
                    </div>

                    <!-- Recent Bike Rentals (with Track & Return buttons) -->
                    <div class="mt-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-history text-primary me-2"></i>Recent Bike Rentals</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr><th>Bike</th><th>Rental Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($recentRentals ?? [] as $rental)
                                    <tr>
                                        <td><strong>{{ $rental->bike->brand ?? 'Bike' }} {{ $rental->bike->model ?? '' }}</strong> <br><small class="text-muted">Duration: {{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</small></td>
                                        <td>{{ $rental->created_at->format('d M Y') }}<br><small class="text-muted">{{ $rental->created_at->format('H:i') }}</small></td>
                                        <td><strong>MWK {{ number_format($rental->total_amount ?? $rental->total_price, 0) }}</strong></td>
                                        <td>
                                            @if($rental->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($rental->status === 'completed')
                                                <span class="badge bg-info">Completed</span>
                                            @elseif($rental->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rental->status === 'active')
                                                <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info mb-1">
                                                    <i class="fas fa-map-marked-alt"></i> Track
                                                </a>
                                                <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Return this bike?')">
                                                        Return
                                                    </button>
                                                </form>
                                            @endif
                                            @if($rental->status === 'pending' && !$rental->is_paid)
                                                <form action="{{ route('payment.manual-verify') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="rental_id" value="{{ $rental->id }}">
                                                    <button type="submit" class="btn btn-sm btn-warning">Verify Payment</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center">No bike rentals yet. <a href="{{ route('user.bikes.index') }}">Rent a bike now</a></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ========== AVAILABLE RIDES SECTION (FULL LIST) ========== -->
                <div id="availableRidesSection" style="display: none;">
                    <h5 class="fw-bold mb-3"><i class="fas fa-car text-primary me-2"></i>All Available Rides</h5>
                    <div class="row g-4">
                        @forelse($availableRides ?? [] as $ride)
                        <div class="col-lg-4 col-md-6">
                            <div class="ride-card" onclick="window.location.href='{{ route('user.bookings.create', $ride) }}'">
                                <div class="card-img-icon">
                                    <i class="fas fa-car-side fa-3x text-primary"></i>
                                </div>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $ride->from_location }}</strong>
                                        <i class="fas fa-arrow-right text-muted"></i>
                                        <strong>{{ $ride->to_location }}</strong>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="far fa-calendar-alt me-1"></i> {{ \Carbon\Carbon::parse($ride->departure_time)->format('d M Y, H:i') }}
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="price-tag">MWK {{ number_format($ride->price, 0) }}</span>
                                        <span class="badge-available">{{ $ride->available_seats }} seats left</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12"><div class="alert alert-info">No rides available at the moment.</div></div>
                        @endforelse
                    </div>
                </div>

                <!-- ========== AVAILABLE BIKES SECTION (FULL LIST) ========== -->
                <div id="availableBikesSection" style="display: none;">
                    <h5 class="fw-bold mb-3"><i class="fas fa-bicycle text-primary me-2"></i>All Available Bikes</h5>
                    <div class="row g-4">
                        @forelse($availableBikes ?? [] as $bike)
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="bike-card" onclick="window.location.href='{{ route('user.bikes.rent', $bike) }}'">
                                <div class="card-img-icon">
                                    <i class="fas fa-bicycle fa-3x text-success"></i>
                                </div>
                                <div class="p-3 text-center">
                                    <h6 class="fw-bold mb-0">{{ $bike->brand }} {{ $bike->model }}</h6>
                                    <small class="text-muted">{{ ucfirst($bike->type) }}</small>
                                    <div class="mt-2">
                                        <span class="price-tag">MWK {{ number_format($bike->price_per_hour, 0) }}/hr</span>
                                    </div>
                                    <div class="small text-muted">Daily: MWK {{ number_format($bike->price_per_day, 0) }}</div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12"><div class="alert alert-info">No bikes available at the moment.</div></div>
                        @endforelse
                    </div>
                </div>

                <!-- ========== MY BOOKINGS SECTION ========== -->
                <div id="bookingsSection" style="display: none;">
                    <h5 class="fw-bold mb-3"><i class="fas fa-calendar-check text-primary me-2"></i>My Ride Bookings</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr><th>Route</th><th>Date</th><th>Seats</th><th>Amount</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($allBookings ?? [] as $booking)
                                <td>
                                    <td>{{ $booking->advertisement->from_location ?? 'N/A' }} → {{ $booking->advertisement->to_location ?? 'N/A' }}</small></td>
                                    <td>{{ \Carbon\Carbon::parse($booking->trip_date)->format('d M Y') }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td>MWK {{ number_format($booking->total_price, 0) }}</td>
                                    <td><span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($booking->status) }}</span></td>
                                </tr>
                                @empty
                                <td><td colspan="5" class="text-center">No ride bookings. <a href="#" onclick="document.querySelector('[data-section=\'available-rides\']').click(); return false;">Book a ride now</a></td></td>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ========== BIKE RENTALS SECTION (FULL) ========== -->
                <div id="bikeRentalsSection" style="display: none;">
                    <h5 class="fw-bold mb-3"><i class="fas fa-bicycle text-primary me-2"></i>My Bike Rentals</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <td><th>Bike</th><th>Rental Date</th><th>Duration</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($allBikeRentals ?? [] as $rental)
                                <tr>
                                    <td>{{ $rental->bike->brand ?? 'Bike' }} {{ $rental->bike->model ?? '' }}</small></td>
                                    <td>{{ $rental->created_at->format('d M Y') }}</td>
                                    <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                                    <td>MWK {{ number_format($rental->total_amount ?? $rental->total_price, 0) }}</td>
                                    <td>
                                        @if($rental->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($rental->status === 'completed')
                                            <span class="badge bg-info">Completed</span>
                                        @elseif($rental->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rental->status === 'active')
                                            <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">Track</a>
                                            <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Return this bike?')">Return</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">No bike rentals. <a href="#" onclick="document.querySelector('[data-section=\'available-bikes\']').click(); return false;">Rent a bike now</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Sidebar navigation
    document.querySelectorAll('.menu-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.menu-item').forEach(link => link.classList.remove('active'));
            this.classList.add('active');
            
            const section = this.getAttribute('data-section');
            const sections = ['overviewSection', 'availableRidesSection', 'availableBikesSection', 'bookingsSection', 'bikeRentalsSection'];
            sections.forEach(sec => {
                const el = document.getElementById(sec);
                if (el) el.style.display = 'none';
            });
            
            if (section === 'overview') document.getElementById('overviewSection').style.display = 'block';
            else if (section === 'available-rides') document.getElementById('availableRidesSection').style.display = 'block';
            else if (section === 'available-bikes') document.getElementById('availableBikesSection').style.display = 'block';
            else if (section === 'bookings') document.getElementById('bookingsSection').style.display = 'block';
            else if (section === 'bike-rentals') document.getElementById('bikeRentalsSection').style.display = 'block';
        });
    });
</script>
@endsection