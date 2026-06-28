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

    /* Timer card styles */
    #rentalTimer .display-6 {
        font-size: 2.5rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
    }
    #rentalTimer .bg-light {
        background-color: #f8f9fa !important;
    }
    #rentalTimer .card-body {
        padding: 1.5rem;
    }
    .btn-group-sm > .btn, .btn-sm { font-size: 0.7rem; padding: 0.2rem 0.5rem; }
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
                    <li><a href="{{ route('user.bookings.index') }}" class="menu-item"><i class="fas fa-calendar-check"></i> My Bookings</a></li>
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
                        <a href="{{ route('search') }}" class="btn btn-outline-primary"><i class="fas fa-car me-2"></i>Book a ride</a>
                        <a href="{{ route('user.bikes.index') }}" class="btn btn-outline-primary"><i class="fas fa-bicycle me-2"></i>Rent a bike</a>
                        <a href="{{ route('subscription.index') }}" class="btn btn-outline-primary"><i class="fas fa-ticket-alt me-2"></i>Subscription</a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary"><i class="fas fa-user-edit me-2"></i>Edit profile</a>
                    </div>

                    <!-- Active Bike Rental Block -->
                    @if(isset($activeRental) && $activeRental)
                        <div class="card mb-4 border-primary">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i> Active Bike Rental</h5>
                                <span class="badge bg-light text-primary">Live</span>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5>{{ $activeRental->bike->brand }} {{ $activeRental->bike->model }}</h5>
                                        <p class="mb-1"><i class="fas fa-clock text-primary"></i> Started: {{ $activeRental->start_time->format('d M Y, H:i') }}</p>
                                        <p><span class="badge bg-success"><i class="fas fa-check-circle"></i> Active</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="rentalTimer" class="text-center p-3 bg-light rounded">
                                            <div class="display-6 fw-bold" id="timerDisplay">00:00:00</div>
                                            <div class="small text-muted">Time Elapsed</div>
                                            <div class="row mt-2">
                                                <div class="col-6">
                                                    <small>Elapsed Time</small><br>
                                                    <strong id="elapsedDisplay">0m 0s</strong>
                                                </div>
                                                <div class="col-6">
                                                    <small>Current Cost</small><br>
                                                    <strong id="costDisplay" class="text-primary">MWK 0.00</strong>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <h5>Total Due: <span id="totalDueDisplay" class="text-primary">MWK 0.00</span></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    <form action="{{ route('user.bike-rentals.return', $activeRental) }}" method="POST" class="d-inline" id="returnForm">
                                        @csrf
                                        <button type="submit" class="btn btn-success" id="returnBtn">
                                            <i class="fas fa-undo-alt me-2"></i> Return Bike
                                        </button>
                                    </form>
                                    <a href="{{ route('tracking.bike', $activeRental) }}" class="btn btn-info">
                                        <i class="fas fa-map-marked-alt me-2"></i> Track
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Subscription Status Alert -->
                    @php
                        $subscription = App\Models\Subscription::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->where('end_date', '>', now())
                            ->first();
                    @endphp

                    @if($subscription)
                        <div class="alert alert-success mb-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <i class="fas fa-ticket-alt fa-2x me-3 float-start"></i>
                                    <div>
                                        <strong>{{ ucfirst($subscription->type) }} Pass Active!</strong><br>
                                        <small>Expires: {{ $subscription->end_date->format('d M Y, H:i') }}</small><br>
                                        <small>Free rides left today: {{ $subscription->getRemainingTodaysRides() }} / {{ $subscription->getDailyLimit() }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-light mt-2 mt-sm-0">Manage</a>
                            </div>
                        </div>
                    @endif

                    <!-- Available Rides Preview -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-car text-primary me-2"></i>Available Rides – One Click to Book</h5>
                        <div class="row g-3">
                            @forelse(($availableRides ?? [])->filter(function($ride) { 
                                return $ride->status === 'approved' && $ride->available_seats > 0; 
                            })->take(3) as $ride)
                            <div class="col-md-4 col-sm-6">
                                <div class="ride-card" onclick="window.location.href='{{ route('user.bookings.create', $ride) }}'">
                                    <div class="card-img-icon">
                                        <i class="fas fa-car-side fa-3x text-primary"></i>
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between">
                                            <strong class="small">{{ Str::limit($ride->fromLocation->name ?? $ride->from_location, 15) }}</strong>
                                            <i class="fas fa-arrow-right text-muted fa-xs"></i>
                                            <strong class="small">{{ Str::limit($ride->toLocation->name ?? $ride->to_location, 15) }}</strong>
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
                            <div class="col-12"><div class="alert alert-info">No rides available at the moment.</div></div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Available Bikes Preview -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-bicycle text-primary me-2"></i>Available Bikes – One Click to Rent</h5>
                        <div class="row g-3">
                            @forelse(($availableBikes ?? [])->filter(function($bike) { 
                                return $bike->status === 'available'; 
                            })->take(4) as $bike)
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
                            <div class="col-12"><div class="alert alert-info">No bikes available at the moment.</div></div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card d-flex align-items-center">
                                <div class="stat-icon me-3"><i class="fas fa-coins"></i></div>
                                <div>
                                    <h3 class="fw-bold mb-0">MWK {{ number_format($totalSpent ?? 0, 0) }}</h3>
                                    <p class="text-muted mb-0">Total Spent</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card d-flex align-items-center">
                                <div class="stat-icon me-3"><i class="fas fa-car"></i></div>
                                <div>
                                    <h3 class="fw-bold mb-0">{{ $totalRides ?? 0 }}</h3>
                                    <p class="text-muted mb-0">Rides Taken</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card d-flex align-items-center">
                                <div class="stat-icon me-3"><i class="fas fa-bicycle"></i></div>
                                <div>
                                    <h3 class="fw-bold mb-0">{{ $totalRentals ?? 0 }}</h3>
                                    <p class="text-muted mb-0">Bikes Rented</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="stat-card d-flex align-items-center">
                                <div class="stat-icon me-3"><i class="fas fa-ticket-alt"></i></div>
                                <div>
                                    <h3 class="fw-bold mb-0">MWK {{ number_format($moneySaved ?? 0, 0) }}</h3>
                                    <p class="text-muted mb-0">Saved with Subscription</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bike Rentals -->
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
                                        <td>
                                            <strong>{{ $rental->bike->brand ?? 'Bike' }} {{ $rental->bike->model ?? '' }}</strong>
                                            <br><small class="text-muted">Duration: {{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</small>
                                        </td>
                                        <td>
                                            {{ $rental->created_at->format('d M Y') }}<br>
                                            <small class="text-muted">{{ $rental->created_at->format('H:i') }}</small>
                                        </td>
                                        <td><strong>MWK {{ number_format($rental->total_amount ?? $rental->total_price, 0) }}</strong></td>
                                        <td>
                                            @if($rental->status === 'active' || $rental->status === 'rented')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($rental->status === 'completed')
                                                <span class="badge bg-info">Completed</span>
                                            @elseif($rental->status === 'pending')
                                                <span class="badge bg-warning">Pending Payment</span>
                                            @elseif($rental->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rental->status === 'active' || $rental->status === 'rented')
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
                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-check-circle me-1"></i> Verify Payment
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($rental->status === 'pending')
                                                <form action="{{ route('user.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this rental?')">
                                                        <i class="fas fa-times-circle me-1"></i> Cancel
                                                    </button>
                                                </form>
                                            @endif

                                            @if($rental->late_fee > 0 && !$rental->late_fee_paid && $rental->status === 'completed')
                                                <a href="{{ route('rentals.pay-late-fee', $rental) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-credit-card me-1"></i> Pay Late Fee
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No bike rentals yet. <a href="{{ route('user.bikes.index') }}">Rent a bike now</a></td>
                                    </tr>
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
                            @if($ride->status === 'approved' && $ride->available_seats > 0)
                            <div class="col-lg-4 col-md-6">
                                <div class="ride-card" onclick="window.location.href='{{ route('user.bookings.create', $ride) }}'">
                                    <div class="card-img-icon">
                                        <i class="fas fa-car-side fa-3x text-primary"></i>
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $ride->fromLocation->name ?? $ride->from_location }}</strong>
                                            <i class="fas fa-arrow-right text-muted"></i>
                                            <strong>{{ $ride->toLocation->name ?? $ride->to_location }}</strong>
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
                            @endif
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
                            @if($bike->status === 'available')
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
                            @endif
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
                                <tr><th>Route</th><th>Date</th><th>Seats</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($allBookings ?? [] as $booking)
                                @php
                                    $tripStatus = $booking->trip_status ?? 'pending';
                                    $isPassenger = $booking->user_id === auth()->id();
                                    $isVehicleOwner = $booking->advertisement->owner_id === auth()->id();
                                @endphp
                                <tr>
                                    <td>
                                        {{ $booking->advertisement->fromLocation->name ?? $booking->advertisement->from_location ?? 'N/A' }}
                                        →
                                        {{ $booking->advertisement->toLocation->name ?? $booking->advertisement->to_location ?? 'N/A' }}
                                        <br><small class="text-muted">
                                            {{ \Carbon\Carbon::parse($booking->advertisement->departure_time)->format('d M Y H:i') }}
                                        </small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->trip_date)->format('d M Y') }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td>MWK {{ number_format($booking->total_price, 0) }}</td>
                                    <td>
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-warning">Pending Payment</span>
                                        @elseif($booking->status === 'confirmed' && $tripStatus === 'pending')
                                            <span class="badge bg-info">Confirmed</span>
                                        @elseif($tripStatus === 'in_progress')
                                            <span class="badge bg-success">In Transit 🚗</span>
                                        @elseif($tripStatus === 'completed')
                                            <span class="badge bg-secondary">Completed ✅</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-sm btn-info mb-1">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($booking->status === 'pending')
                                            <a href="{{ route('user.bookings.payment.initiate', $booking) }}" class="btn btn-sm btn-success mb-1">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Cancel this booking?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @if(!$booking->is_paid)
                                                <form action="{{ route('payment.manual-verify') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" class="btn btn-sm btn-warning mb-1">
                                                        <i class="fas fa-check-circle me-1"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif

                                        @if($booking->status === 'confirmed')
                                            @if($tripStatus === 'pending')
                                                @if($isPassenger || $isVehicleOwner)
                                                    <form action="{{ route('user.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Start this trip?')" title="Start Trip">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            @if($tripStatus === 'in_progress')
                                                @if($isPassenger || $isVehicleOwner)
                                                    <form action="{{ route('user.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary mb-1" onclick="return confirm('Complete this trip?')" title="Complete Trip">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif

                                        @if($booking->booking_type === 'subscription')
                                            <span class="badge bg-success">Free (Subscription)</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No ride bookings yet.</td>
                                </tr>
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
                                <tr><th>Bike</th><th>Rental Date</th><th>Duration</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($allBikeRentals ?? [] as $rental)
                                <tr>
                                    <td>{{ $rental->bike->brand ?? 'Bike' }} {{ $rental->bike->model ?? '' }}</td>
                                    <td>{{ $rental->created_at->format('d M Y') }}</td>
                                    <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                                    <td>MWK {{ number_format($rental->total_amount ?? $rental->total_price, 0) }}</td>
                                    <td>
                                        @if($rental->status === 'active' || $rental->status === 'rented')
                                            <span class="badge bg-success">Active</span>
                                        @elseif($rental->status === 'completed')
                                            <span class="badge bg-info">Completed</span>
                                        @elseif($rental->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($rental->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rental->status === 'active' || $rental->status === 'rented')
                                            <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">Track</a>
                                            <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Return</button>
                                            </form>
                                        @endif
                                        @if($rental->status === 'pending')
                                            <form action="{{ route('user.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this rental?')">Cancel</button>
                                            </form>
                                        @endif
                                        @if($rental->late_fee > 0 && !$rental->late_fee_paid && $rental->status === 'completed')
                                            <a href="{{ route('rentals.pay-late-fee', $rental) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-credit-card me-1"></i> Pay Late Fee
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">No bike rentals yet.</td></tr>
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
            const href = this.getAttribute('href');
            if (href && href !== '#' && href !== '') {
                return;
            }
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

    // ============================================================
    // ACTIVE BIKE RENTAL TIMER - COUNTING UP
    // ============================================================
    @if(isset($activeRental) && $activeRental && $activeRental->start_time)
    (function() {
        console.log('Timer initialization started...');
        
        const rentalData = @json($activeRental);
        console.log('Rental data:', rentalData);
        
        if (!rentalData) {
            console.error('No rental data found');
            return;
        }

        // Get start time from rental
        const startTime = new Date(rentalData.start_time).getTime();
        const ratePerMinute = parseFloat(rentalData.rate_per_minute) || 2.00;
        
        console.log('Start time:', new Date(startTime));
        console.log('Rate per minute:', ratePerMinute);
        
        // Get DOM elements
        const timerDisplay = document.getElementById('timerDisplay');
        const elapsedDisplay = document.getElementById('elapsedDisplay');
        const costDisplay = document.getElementById('costDisplay');
        const totalDueDisplay = document.getElementById('totalDueDisplay');
        const returnBtn = document.getElementById('returnBtn');
        
        if (!timerDisplay) {
            console.error('Timer display element not found');
            return;
        }
        
        // Format time as HH:MM:SS
        function formatTime(seconds) {
            if (seconds < 0) seconds = 0;
            const h = String(Math.floor(seconds / 3600)).padStart(2, '0');
            const m = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
            const s = String(Math.floor(seconds % 60)).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }
        
        // Format time short
        function formatTimeShort(seconds) {
            if (seconds < 0) seconds = 0;
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            
            if (hours > 0) {
                return hours + 'h ' + minutes + 'm ' + secs + 's';
            } else if (minutes > 0) {
                return minutes + 'm ' + secs + 's';
            }
            return secs + 's';
        }
        
        function updateTimer() {
            try {
                const now = Date.now();
                const elapsedSeconds = Math.floor((now - startTime) / 1000);
                const displaySeconds = Math.max(0, elapsedSeconds);
                
                // Update timer display - counting UP
                timerDisplay.textContent = formatTime(displaySeconds);
                timerDisplay.style.color = '#28a745';
                timerDisplay.style.fontWeight = '700';
                
                // Calculate cost (MWK 2 per minute)
                const elapsedMinutes = Math.ceil(displaySeconds / 60);
                const currentCost = elapsedMinutes * ratePerMinute;
                
                // Update elapsed display
                if (elapsedDisplay) {
                    elapsedDisplay.textContent = formatTimeShort(displaySeconds);
                }
                
                // Update cost display
                if (costDisplay) {
                    costDisplay.textContent = 'MWK ' + currentCost.toFixed(2);
                }
                
                // Update total due
                if (totalDueDisplay) {
                    totalDueDisplay.textContent = 'MWK ' + currentCost.toFixed(2);
                }
                
            } catch (error) {
                console.error('Timer update error:', error);
            }
        }
        
        // Initial update
        updateTimer();
        
        // Set interval to update every second
        const interval = setInterval(updateTimer, 1000);
        console.log('Timer started, updating every second');
        
        // Handle return form submission
        const returnForm = document.getElementById('returnForm');
        if (returnForm && returnBtn) {
            returnForm.addEventListener('submit', function() {
                returnBtn.disabled = true;
                returnBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Processing...';
            });
        }
        
        // Cleanup interval when page changes
        window.addEventListener('beforeunload', function() {
            clearInterval(interval);
        });
        
    })();
    @endif
</script>
@endsection