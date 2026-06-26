@extends('layouts.vehicle-owner')

@section('title', 'Vehicle Owner Dashboard - Mzuni UNITRAS')

@push('styles')
<style>
    .dashboard-wrapper {
        padding: 1.5rem;
        background: #f8fafc;
        min-height: calc(100vh - 70px);
    }

    /* Welcome Section */
    .welcome-section {
        background: linear-gradient(135deg, #0D6EFD, #0a58ca);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .welcome-section::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .welcome-section .greeting {
        font-size: 1.8rem;
        font-weight: 700;
    }
    .welcome-section .sub-text {
        opacity: 0.9;
        font-size: 1rem;
    }
    .welcome-section .online-badge {
        background: rgba(255,255,255,0.15);
        padding: 0.4rem 1.2rem;
        border-radius: 50px;
        font-size: 0.8rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Stats Cards - Modern Glass Design */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card.blue::before { background: linear-gradient(90deg, #0D6EFD, #3b82f6); }
    .stat-card.green::before { background: linear-gradient(90deg, #198754, #34d399); }
    .stat-card.orange::before { background: linear-gradient(90deg, #fd7e14, #fbbf24); }
    .stat-card.purple::before { background: linear-gradient(90deg, #6f42c1, #a78bfa); }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.12);
        border-color: #0D6EFD;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .stat-icon.blue { background: #DBEAFE; color: #1D4ED8; }
    .stat-icon.green { background: #D1FAE5; color: #065F46; }
    .stat-icon.orange { background: #FEF3C7; color: #92400E; }
    .stat-icon.purple { background: #EDE9FE; color: #5B21B6; }

    .stat-number {
        font-size: 1.8rem;
        font-weight: 800;
        color: #1E293B;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }
    .stat-label {
        font-size: 0.85rem;
        color: #64748B;
        font-weight: 500;
    }
    .stat-change {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        display: inline-block;
    }
    .stat-change.up { background: #D1FAE5; color: #065F46; }
    .stat-change.down { background: #FEE2E2; color: #991B1B; }

    /* Quick Action Cards - Modern Grid */
    .action-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .action-card {
        background: white;
        border-radius: 14px;
        padding: 1.2rem 1.5rem;
        border: 1px solid #E2E8F0;
        text-decoration: none;
        color: #1E293B;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
    }
    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px -10px rgba(0,0,0,0.08);
        border-color: #0D6EFD;
        color: #0D6EFD;
    }
    .action-icon-sm {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .action-icon-sm.blue { background: #DBEAFE; color: #1D4ED8; }
    .action-icon-sm.green { background: #D1FAE5; color: #065F46; }
    .action-icon-sm.orange { background: #FEF3C7; color: #92400E; }
    .action-icon-sm.purple { background: #EDE9FE; color: #5B21B6; }

    .action-text h6 { font-weight: 600; font-size: 0.9rem; margin: 0; }
    .action-text small { font-size: 0.7rem; color: #94A3B8; }

    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .section-header h6 {
        font-weight: 700;
        font-size: 1rem;
        color: #1E293B;
        margin: 0;
    }
    .section-header .view-all {
        font-size: 0.8rem;
        color: #0D6EFD;
        text-decoration: none;
        font-weight: 500;
    }
    .section-header .view-all:hover { color: #0a58ca; }

    /* Table Cards */
    .table-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        overflow: hidden;
        height: 100%;
    }
    .table-card .table {
        margin-bottom: 0;
    }
    .table-card .table td {
        padding: 0.8rem 1.2rem;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #F1F5F9;
    }
    .table-card .table tr:last-child td { border-bottom: none; }
    .table-card .table tr:hover td { background: #FAFBFC; }

    .empty-state {
        padding: 2.5rem 1.5rem;
        text-align: center;
        color: #94A3B8;
    }
    .empty-state i {
        font-size: 2.5rem;
        color: #CBD5E1;
        margin-bottom: 0.8rem;
        display: block;
    }

    /* Status Badges */
    .badge-status {
        padding: 0.3rem 0.8rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.7rem;
    }

    @media (max-width: 768px) {
        .welcome-section { padding: 1.5rem; }
        .welcome-section .greeting { font-size: 1.3rem; }
        .stat-number { font-size: 1.4rem; }
        .action-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">

    {{-- Welcome Section --}}
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="greeting">👋 Welcome back, {{ Auth::user()->name }}!</div>
                <div class="sub-text mt-1">Here's what's happening with your business today.</div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="online-badge">
                    <i class="fas fa-circle text-success me-1" style="font-size:0.5rem;"></i> Online
                </span>
                <div class="mt-2">
                    <span class="badge bg-light text-dark" style="font-size:0.7rem;">
                        <i class="far fa-calendar-alt me-1"></i> {{ now()->format('l, d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card blue">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Total Earnings</div>
                        <div class="stat-number">MWK {{ number_format($totalEarnings ?? 0, 2) }}</div>
                        <span class="stat-change up mt-2">
                            <i class="fas fa-arrow-up me-1"></i> {{ $completedTrips ?? 0 }} trips
                        </span>
                    </div>
                    <div class="stat-icon blue">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card green">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Active Vehicles</div>
                        <div class="stat-number">{{ $activeVehicles ?? 0 }}</div>
                        <span class="stat-change up mt-2">
                            <i class="fas fa-check-circle me-1"></i> Approved
                        </span>
                    </div>
                    <div class="stat-icon green">
                        <i class="fas fa-car"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card orange">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Active Ads</div>
                        <div class="stat-number">{{ $activeAds->count() ?? 0 }}</div>
                        <span class="stat-change up mt-2">
                            <i class="fas fa-circle me-1"></i> Live
                        </span>
                    </div>
                    <div class="stat-icon orange">
                        <i class="fas fa-ad"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="stat-card purple">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stat-label">Pending Bookings</div>
                        <div class="stat-number">{{ $pendingBookings ?? 0 }}</div>
                        <span class="stat-change down mt-2">
                            <i class="fas fa-clock me-1"></i> Awaiting
                        </span>
                    </div>
                    <div class="stat-icon purple">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="action-grid">
        <a href="{{ route('vehicle-owner.vehicles.create') }}" class="action-card">
            <div class="action-icon-sm blue"><i class="fas fa-plus-circle"></i></div>
            <div class="action-text">
                <h6>Add Vehicle</h6>
                <small>Register a new vehicle</small>
            </div>
        </a>
        <a href="{{ route('vehicle-owner.advertisements.create') }}" class="action-card">
            <div class="action-icon-sm green"><i class="fas fa-share-alt"></i></div>
            <div class="action-text">
                <h6>Post a Ride</h6>
                <small>Publish a new ride ad</small>
            </div>
        </a>
        <a href="{{ route('vehicle-owner.bookings.index') }}" class="action-card">
            <div class="action-icon-sm orange"><i class="fas fa-calendar-check"></i></div>
            <div class="action-text">
                <h6>View Bookings</h6>
                <small>Manage ride bookings</small>
            </div>
        </a>
        <a href="{{ route('vehicle-owner.advertisements.index') }}" class="action-card">
            <div class="action-icon-sm purple"><i class="fas fa-list-ul"></i></div>
            <div class="action-text">
                <h6>Manage Ads</h6>
                <small>View all advertisements</small>
            </div>
        </a>
        <a href="{{ route('vehicle-owner.vehicles.index') }}" class="action-card">
            <div class="action-icon-sm purple"><i class="fas fa-car"></i></div>
            <div class="action-text">
                <h6>My Vehicles</h6>
                <small>Manage your fleet</small>
            </div>
        </a>
        <a href="{{ route('vehicle-owner.earnings') }}" class="action-card">
            <div class="action-icon-sm green"><i class="fas fa-coins"></i></div>
            <div class="action-text">
                <h6>Earnings</h6>
                <small>View your earnings</small>
            </div>
        </a>
    </div>

    {{-- Recent Activity Tables --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="table-card">
                <div class="section-header px-3 pt-3">
                    <h6><i class="fas fa-car text-primary me-2"></i>My Vehicles</h6>
                    <a href="{{ route('vehicle-owner.vehicles.index') }}" class="view-all">View All →</a>
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
                                    <td class="text-end">
                                        <span class="badge-status {{ $vehicle->is_approved ? 'bg-success text-white' : 'bg-warning text-dark' }}">
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
                            <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-sm btn-primary mt-2">Add Vehicle</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="table-card">
                <div class="section-header px-3 pt-3">
                    <h6><i class="fas fa-ad text-primary me-2"></i>Active Ads</h6>
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="view-all">View All →</a>
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
                                    <td class="text-end">
                                        <span class="badge-status {{ $ad->trip_status === 'in_progress' ? 'bg-success text-white' : 'bg-info text-white' }}">
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
                            <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-sm btn-primary mt-2">Post a Ride</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="table-card">
                <div class="section-header px-3 pt-3">
                    <h6><i class="fas fa-calendar-alt text-primary me-2"></i>Recent Bookings</h6>
                    <a href="{{ route('vehicle-owner.bookings.index') }}" class="view-all">View All →</a>
                </div>
                <div class="table-responsive">
                    @if(($recentBookings ?? [])->count() > 0)
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Route</th>
                                    <th>Seats</th>
                                    <th>Status</th>
                                    <th>Trip</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->user->name ?? 'N/A' }}</strong>
                                        <div class="small text-muted">{{ $booking->user->email ?? '' }}</div>
                                    </td>
                                    <td>{{ $booking->pickup_point }} → {{ $booking->dropoff_point }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td>
                                        <span class="badge-status bg-{{ $booking->status === 'pending' ? 'warning text-dark' : ($booking->status === 'confirmed' ? 'info text-white' : 'success text-white') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $tripStatus = $booking->trip_status ?? 'pending'; @endphp
                                        <span class="badge-status bg-{{ $tripStatus === 'in_progress' ? 'success text-white' : ($tripStatus === 'completed' ? 'secondary text-white' : 'secondary text-white') }}">
                                            {{ ucfirst($tripStatus) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('vehicle-owner.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($booking->status === 'confirmed' && $tripStatus === 'pending')
                                            <form action="{{ route('vehicle-owner.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">
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