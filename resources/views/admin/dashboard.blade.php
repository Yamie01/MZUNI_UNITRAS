@extends('layouts.admin')

@section('title', 'Admin Dashboard - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard Overview</h1>
        <div>
            <span class="text-muted">Last updated: {{ now()->format('d M Y H:i') }}</span>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-label">Total Users</div>
                <div class="stat-value">{{ $stats['total_users'] ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ $stats['users_growth'] ?? 0 }}% from last month
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-car"></i>
                </div>
                <div class="stat-label">Total Vehicles</div>
                <div class="stat-value">{{ $stats['total_vehicles'] ?? 0 }}</div>
                <div class="stat-change text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $stats['pending_vehicles'] ?? 0 }} pending approval
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-bicycle"></i>
                </div>
                <div class="stat-label">Total Bikes</div>
                <div class="stat-value">{{ $stats['total_bikes'] ?? 0 }}</div>
                <div class="stat-change text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $stats['available_bikes'] ?? 0 }} available
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-label">Total Bookings</div>
                <div class="stat-value">{{ $stats['total_bookings'] ?? 0 }}</div>
                <div class="stat-change">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ $stats['bookings_growth'] ?? 0 }}% from last month
                </div>
            </div>
        </div>
    </div>
    
    <!-- Revenue Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-label">Rental Revenue</div>
                <div class="stat-value">MWK {{ number_format($stats['rental_revenue'] ?? 0, 2) }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-chart-line me-1"></i>
                    From {{ $stats['completed_rentals'] ?? 0 }} completed rentals
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-label">Booking Revenue</div>
                <div class="stat-value">MWK {{ number_format($stats['booking_revenue'] ?? 0, 2) }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-chart-line me-1"></i>
                    From {{ $stats['completed_bookings'] ?? 0 }} completed trips
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-total"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">MWK {{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                <div class="stat-change text-success">
                    <i class="fas fa-chart-line me-1"></i>
                    Rentals + Bookings
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-users mb-2 d-block fa-2x"></i>
                                Manage Users
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.advertisements.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-ad mb-2 d-block fa-2x"></i>
                                Review Ads
                                @if(($stats['pending_ads'] ?? 0) > 0)
                                    <span class="badge bg-danger">{{ $stats['pending_ads'] }}</span>
                                @endif
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-car mb-2 d-block fa-2x"></i>
                                View Vehicles
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.bikes.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-bicycle mb-2 d-block fa-2x"></i>
                                Manage Bikes
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.bike-rentals.index') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fas fa-calendar mb-2 d-block fa-2x"></i>
                                Bike Rentals
                            </a>
                        </div>
                        <div class="col-md-2 col-sm-4">
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-dark w-100 py-3">
                                <i class="fas fa-calendar-check mb-2 d-block fa-2x"></i>
                                All Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers ?? [] as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            {{ $user->name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($user->user_type) }}</span>
                                    </td>
                                    <td>
                                        @if($user->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($user->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No users found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Bike Rentals</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rental Code</th>
                                    <th>Bike</th>
                                    <th>Renter</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentRentals ?? [] as $rental)
                                <tr>
                                    <td>{{ $rental->rental_code }}</td>
                                    <td>{{ $rental->bike->brand }} {{ $rental->bike->model }}</td>
                                    <td>{{ $rental->user->name }}</td>
                                    <td>MWK {{ number_format($rental->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $rental->status === 'active' ? 'success' : ($rental->status === 'completed' ? 'info' : 'warning') }}">
                                            {{ ucfirst($rental->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($rental->status === 'active')
                                            <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-map-marked-alt"></i> Track
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recent rentals</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        transition: all 0.3s;
        border: 1px solid rgba(0,0,0,0.05);
        height: 100%;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 12px rgba(0,0,0,0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #00529b, #003f75);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    
    .stat-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin: 5px 0;
    }
    
    .stat-change {
        font-size: 0.8rem;
        color: #28a745;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #00529b, #003f75);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 12px;
    }
</style>
@endsection