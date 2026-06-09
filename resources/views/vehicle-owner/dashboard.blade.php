@extends('layouts.app')

@section('title', 'Vehicle Owner Dashboard - Mzuni UNITRAS')

@push('styles')
<style>
    :root {
        --primary: #00529b;
        --primary-dark: #003f75;
        --secondary: #ff6b35;
        --sidebar-width: 280px;
    }
    
    .dashboard-wrapper {
        display: flex;
        min-height: calc(100vh - 70px);
        background: #f8fafc;
    }
    
    /* Sidebar Styles */
    .dashboard-sidebar {
        width: var(--sidebar-width);
        background: white;
        border-right: 1px solid #e2edf2;
        padding: 1.5rem 0;
        position: sticky;
        top: 70px;
        height: calc(100vh - 70px);
        overflow-y: auto;
    }
    
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-nav li {
        margin-bottom: 4px;
    }
    
    .sidebar-nav a {
        display: flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        color: #4a6272;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
        gap: 12px;
    }
    
    .sidebar-nav a i {
        width: 24px;
        font-size: 1.1rem;
    }
    
    .sidebar-nav a:hover {
        background: #f1f5f9;
        color: var(--primary);
    }
    
    .sidebar-nav a.active {
        background: #ebf8ff;
        color: var(--primary);
        border-right: 3px solid var(--primary);
    }
    
    /* Main Content */
    .dashboard-main {
        flex: 1;
        padding: 1.5rem;
        overflow-x: auto;
    }
    
    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: 0.2s;
        height: 100%;
        border: 1px solid #e2edf2;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 12px 20px -10px rgba(0,82,155,0.15); }
    .stat-icon { width: 48px; height: 48px; background: var(--primary); border-radius: 30px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; }
    
    /* Quick Action Buttons */
    .action-btn { border-radius: 60px; padding: 0.6rem 1.2rem; font-weight: 500; transition: all 0.2s; }
    .btn-outline-primary-custom { border: 1px solid var(--primary); color: var(--primary); background: white; }
    .btn-outline-primary-custom:hover { background: var(--primary); color: white; }
    
    /* Lists */
    .list-group-item { border-left: 3px solid transparent; transition: 0.2s; }
    .list-group-item:hover { border-left-color: var(--primary); background: #f8fafc; }
    .recent-item { background: white; border-radius: 20px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid var(--primary); }
    
    @media (max-width: 768px) {
        .dashboard-wrapper { flex-direction: column; }
        .dashboard-sidebar { width: 100%; height: auto; position: relative; top: 0; }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <div class="text-center mb-4">
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 70px; height: 70px;">
                <i class="fas fa-user-tie fa-2x"></i>
            </div>
            <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
            <small class="text-muted">Vehicle Owner</small>
        </div>
        <hr>
        <ul class="sidebar-nav">
            <li><a href="{{ route('vehicle-owner.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('vehicle-owner.vehicles.index') }}"><i class="fas fa-car"></i> My Vehicles</a></li>
            <li><a href="{{ route('vehicle-owner.advertisements.index') }}"><i class="fas fa-ad"></i> My Ads</a></li>
            <li><a href="{{ route('vehicle-owner.bookings.index') }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="{{ route('vehicle-owner.earnings') }}"><i class="fas fa-coins"></i> Earnings</a></li>
            <li><a href="{{ route('profile.edit') }}"><i class="fas fa-user-circle"></i> Profile</a></li>
            <li><hr></li>
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>

    <!-- Main Content -->
    <div class="dashboard-main">
        <!-- Welcome & Balance -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-0">Welcome back, {{ Auth::user()->name }}! 😊</h4>
                <p class="text-muted">Here’s what’s happening with your business today.</p>
            </div>
            <div class="text-end">
                <div class="fw-bold">Total Earnings: <span class="text-primary">MWK {{ number_format($earnings ?? 0, 2) }}</span></div>
                <small class="text-muted">From {{ $completedTrips ?? 0 }} completed trips</small>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon me-3"><i class="fas fa-car-side"></i></div>
                    <div><h3 class="fw-bold mb-0">{{ $vehicles->count() ?? 0 }}</h3><p class="text-muted mb-0">My Vehicles</p></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon me-3"><i class="fas fa-ad"></i></div>
                    <div><h3 class="fw-bold mb-0">{{ $activeAds->count() ?? 0 }}</h3><p class="text-muted mb-0">Active Ads</p></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon me-3"><i class="fas fa-calendar-check"></i></div>
                    <div><h3 class="fw-bold mb-0">{{ $totalBookings ?? 0 }}</h3><p class="text-muted mb-0">Total Bookings</p></div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card d-flex align-items-center">
                    <div class="stat-icon me-3"><i class="fas fa-coins"></i></div>
                    <div><h3 class="fw-bold mb-0 text-success">MWK {{ number_format($earnings ?? 0, 2) }}</h3><p class="text-muted mb-0">Total Earnings</p></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-4">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn action-btn btn-primary"><i class="fas fa-plus-circle me-2"></i>Add Vehicle</a>
                <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn action-btn btn-outline-primary-custom"><i class="fas fa-car me-2"></i>Post a Ride</a>
                <a href="{{ route('vehicle-owner.bookings.index') }}" class="btn action-btn btn-outline-secondary"><i class="fas fa-ticket-alt me-2"></i>View Bookings</a>
                <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn action-btn btn-outline-secondary"><i class="fas fa-list me-2"></i>Manage Ads</a>
                <a href="{{ route('search') }}" class="btn action-btn btn-info text-white"><i class="fas fa-search me-2"></i>Find a Ride (as passenger)</a>
            </div>
        </div>

        <div class="row">
            <!-- My Vehicles -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-car me-2"></i>My Vehicles</span>
                        <a href="{{ route('vehicle-owner.vehicles.index') }}" class="btn btn-sm btn-outline-primary">Manage Vehicles</a>
                    </div>
                    <div class="card-body p-0">
                        @if($vehicles->count())
                            <div class="list-group list-group-flush">
                                @foreach($vehicles as $vehicle)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $vehicle->brand }} {{ $vehicle->model }}</strong>
                                            <div class="small text-muted">{{ $vehicle->plate_number }} • {{ $vehicle->year }}</div>
                                        </div>
                                        <span class="badge {{ $vehicle->is_approved ? 'bg-success' : 'bg-warning' }}">
                                            {{ $vehicle->is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-car fa-2x text-muted mb-2"></i>
                                <p>No vehicles registered yet.</p>
                                <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-sm btn-primary">Add Vehicle</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Active Ads -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-ad me-2"></i>Active Ride Ads</span>
                        <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-sm btn-outline-primary">Post a Ride</a>
                    </div>
                    <div class="card-body p-0">
                        @if($activeAds->count())
                            <div class="list-group list-group-flush">
                                @foreach($activeAds as $ad)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>{{ $ad->from_location }} → {{ $ad->to_location }}</strong>
                                                <div class="small text-muted">
                                                    {{ \Carbon\Carbon::parse($ad->departure_time)->format('d M Y, H:i') }} •
                                                    {{ $ad->available_seats }} seats • MWK {{ number_format($ad->price) }}
                                                </div>
                                            </div>
                                            <a href="{{ route('vehicle-owner.advertisements.edit', $ad) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-ad fa-2x text-muted mb-2"></i>
                                <p>No active ride ads. Post your first ride!</p>
                                <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-sm btn-primary">Post a Ride</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Booking Requests -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Recent Booking Requests</div>
            <div class="card-body">
                @if($recentBookings->count())
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr><th>Passenger</th><th>Route</th><th>Seats</th><th>Amount</th><th>Status</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->user->name }}</td>
                                    <td>{{ $booking->advertisement->from_location }} → {{ $booking->advertisement->to_location }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td>MWK {{ number_format($booking->total_price) }}</td>
                                    <td><span class="badge bg-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'info' : 'success') }}">{{ ucfirst($booking->status) }}</span></td>
                                    <td>
                                        @if($booking->status == 'pending')
                                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="confirmed">
                                                <button class="btn btn-sm btn-success">Accept</button>
                                            </form>
                                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No booking requests yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection