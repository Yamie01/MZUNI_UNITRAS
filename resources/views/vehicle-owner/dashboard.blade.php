@extends('layouts.vehicle-owner')

@section('title', 'Vehicle Owner Dashboard - Mzuni UNITRAS')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        border: 1px solid #E2E8F0;
        transition: all 0.25s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px -10px rgba(0,0,0,0.08);
        border-color: #0D6EFD;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .stat-icon.blue { background: #DBEAFE; color: #1D4ED8; }
    .stat-icon.green { background: #D1FAE5; color: #065F46; }
    .stat-icon.orange { background: #FEF3C7; color: #92400E; }
    .stat-icon.purple { background: #EDE9FE; color: #5B21B6; }

    .stat-number {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1E293B;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #64748B;
        font-weight: 500;
    }

    .action-card {
        background: white;
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        border: 1px solid #E2E8F0;
        text-decoration: none;
        color: #1E293B;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 16px;
        cursor: pointer;
        height: 100%;
    }
    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px -10px rgba(0,0,0,0.08);
        border-color: #0D6EFD;
        color: #0D6EFD;
    }
    .action-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .action-icon.blue { background: #DBEAFE; color: #1D4ED8; }
    .action-icon.green { background: #D1FAE5; color: #065F46; }
    .action-icon.orange { background: #FEF3C7; color: #92400E; }
    .action-icon.purple { background: #EDE9FE; color: #5B21B6; }
    .action-icon.red { background: #FEE2E2; color: #991B1B; }

    .action-text h6 {
        font-weight: 600;
        font-size: 0.9rem;
        margin: 0;
        color: #1E293B;
    }
    .action-text small {
        font-size: 0.75rem;
        color: #94A3B8;
    }

    .table-container {
        background: white;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        overflow: hidden;
    }
    .table-container .table-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #E2E8F0;
        background: #FAFBFC;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .table-container .table-header h6 {
        font-weight: 600;
        margin: 0;
        color: #1E293B;
    }
    .table-container .table {
        margin-bottom: 0;
    }
    .table-container .table td {
        padding: 0.8rem 1.5rem;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #F1F5F9;
    }
    .table-container .table tr:last-child td { border-bottom: none; }
    .table-container .table tr:hover td { background: #FAFBFC; }

    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: #94A3B8;
    }
    .empty-state i {
        font-size: 3rem;
        color: #CBD5E1;
        margin-bottom: 1rem;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Top Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Welcome back, {{ Auth::user()->name }}! 🎉</h4>
            <p class="text-muted">Here's what's happening with your business today.</p>
        </div>
        <div>
            <span class="badge bg-success bg-opacity-10 text-success p-2 px-3">
                <i class="fas fa-circle me-1" style="font-size:0.5rem;"></i> Online
            </span>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon blue me-3"><i class="fas fa-wallet"></i></div>
                <div>
                    <div class="stat-number">MWK {{ number_format($totalEarnings ?? 0, 2) }}</div>
                    <div class="stat-label">Total Earnings</div>
                    <small class="text-muted">{{ $completedTrips ?? 0 }} completed trips</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon green me-3"><i class="fas fa-car"></i></div>
                <div>
                    <div class="stat-number">{{ $activeVehicles ?? 0 }}</div>
                    <div class="stat-label">Active Vehicles</div>
                    <small class="text-muted">Approved & available</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon orange me-3"><i class="fas fa-ad"></i></div>
                <div>
                    <div class="stat-number">{{ $activeAds->count() ?? 0 }}</div>
                    <div class="stat-label">Active Ads</div>
                    <small class="text-muted">Live advertisements</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon purple me-3"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <div class="stat-number">{{ $pendingBookings ?? 0 }}</div>
                    <div class="stat-label">Pending Bookings</div>
                    <small class="text-muted">Awaiting confirmation</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('vehicle-owner.vehicles.create') }}" class="action-card">
                <div class="action-icon blue"><i class="fas fa-plus-circle"></i></div>
                <div class="action-text">
                    <h6>Add Vehicle</h6>
                    <small>Register a new vehicle</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('vehicle-owner.advertisements.create') }}" class="action-card">
                <div class="action-icon green"><i class="fas fa-share-alt"></i></div>
                <div class="action-text">
                    <h6>Post a Ride</h6>
                    <small>Publish a new ride ad</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('vehicle-owner.bookings.index') }}" class="action-card">
                <div class="action-icon orange"><i class="fas fa-calendar-check"></i></div>
                <div class="action-text">
                    <h6>View Bookings</h6>
                    <small>Manage ride bookings</small>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('vehicle-owner.advertisements.index') }}" class="action-card">
                <div class="action-icon purple"><i class="fas fa-list-ul"></i></div>
                <div class="action-text">
                    <h6>Manage Ads</h6>
                    <small>View all advertisements</small>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Vehicles & Ads --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="table-container">
                <div class="table-header">
                    <h6><i class="fas fa-car me-2"></i>My Vehicles</h6>
                    <a href="{{ route('vehicle-owner.vehicles.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    @if(($recentVehicles ?? [])->count() > 0)
                        <table class="table">
                            <tbody>
                                @foreach($recentVehicles as $vehicle)
                                <tr>
                                    <td>
                                        <strong>{{ $vehicle->model }}</strong>
                                        <div class="small text-muted">{{ $vehicle->registration_number }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $vehicle->is_approved ? 'bg-success' : 'bg-warning' }}">
                                            {{ $vehicle->is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-car"></i>
                            <p>No vehicles registered yet.</p>
                            <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-sm btn-primary">Add Vehicle</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-container">
                <div class="table-header">
                    <h6><i class="fas fa-ad me-2"></i>Active Ads</h6>
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    @if(($activeAds ?? [])->count() > 0)
                        <table class="table">
                            <tbody>
                                @foreach($activeAds as $ad)
                                <tr>
                                    <td>
                                        <strong>{{ $ad->from_location }} → {{ $ad->to_location }}</strong>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($ad->departure_time)->format('d M Y, H:i') }}
                                            • {{ $ad->available_seats }} seats
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ad->trip_status === 'in_progress' ? 'bg-success' : 'bg-info' }}">
                                            {{ ucfirst($ad->trip_status ?? 'scheduled') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-ad"></i>
                            <p>No active advertisements.</p>
                            <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-sm btn-primary">Post a Ride</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h6><i class="fas fa-calendar-alt me-2"></i>Recent Bookings</h6>
                    <a href="{{ route('vehicle-owner.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="table-responsive">
                    @if(($recentBookings ?? [])->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Seats</th>
                                    <th>Status</th>
                                    <th>Trip Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->pickup_point }} → {{ $booking->dropoff_point }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status === 'pending' ? 'warning' : ($booking->status === 'confirmed' ? 'info' : 'success') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $tripStatus = $booking->trip_status ?? 'pending'; @endphp
                                        <span class="badge bg-{{ $tripStatus === 'in_progress' ? 'success' : ($tripStatus === 'completed' ? 'secondary' : 'secondary') }}">
                                            {{ ucfirst($tripStatus) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('vehicle-owner.bookings.show', $booking) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->status === 'confirmed' && $tripStatus === 'pending')
                                            <form action="{{ route('vehicle-owner.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <p>No recent bookings.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection