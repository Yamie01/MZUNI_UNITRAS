@extends('layouts.vehicle-owner')

@section('title', 'Manage Bookings - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Bookings</h6>
                    <h4>{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h4>{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Confirmed</h6>
                    <h4>{{ $stats['confirmed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>In Progress</h6>
                    <h4>{{ $stats['in_progress'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h4>{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h6>Total Revenue</h6>
                    <h4>MWK {{ number_format($stats['total_revenue'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vehicle-owner.bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="trip_status" class="form-select">
                        <option value="">All Trip Status</option>
                        <option value="scheduled" {{ request('trip_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ request('trip_status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('trip_status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5>Bookings for My Advertisements</h5>
        </div>
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Route</th>
                                <th>Seats</th>
                                <th>Total Price</th>
                                <th>Payment Status</th>
                                <th>Trip Status</th>
                                <th>Booking Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>
                                    <strong>{{ $booking->user->name ?? 'N/A' }}</strong><br>
                                    <small>{{ $booking->user->phone ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $booking->advertisement->from_location ?? 'N/A' }} → {{ $booking->advertisement->to_location ?? 'N/A' }}</small></td>
                                <td>{{ $booking->number_of_seats }}</td>
                                <td><strong>MWK {{ number_format($booking->total_price, 2) }}</strong><br>
                                    <small class="text-muted">You earn: MWK {{ number_format($booking->owner_earnings ?? 0, 2) }}</small>
                                </td>
                                <td>
                                    @if($booking->status == 'confirmed')
                                        <span class="badge bg-success">Confirmed</span>
                                    @elseif($booking->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($booking->status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->trip_status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($booking->trip_status == 'in_progress')
                                        <span class="badge bg-primary">In Progress</span>
                                    @elseif($booking->trip_status == 'scheduled')
                                        <span class="badge bg-secondary">Scheduled</span>
                                    @endif
                                    @if($booking->trip_completed_at)
                                        <br><small>{{ $booking->trip_completed_at->format('d M H:i') }}</small>
                                    @endif
                                </td>
                                <td>{{ $booking->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('vehicle-owner.bookings.show', $booking) }}" class="btn btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($booking->status == 'pending')
                                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="btn btn-success" title="Confirm" onclick="return confirm('Confirm this booking?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-danger" title="Cancel" onclick="return confirm('Cancel this booking?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @elseif($booking->status == 'confirmed' && $booking->trip_status == 'scheduled')
                                            <form action="{{ route('vehicle-owner.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" title="Start Trip" onclick="return confirm('Start this trip?')">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            </form>
                                        @elseif($booking->trip_status == 'in_progress')
                                            <form action="{{ route('vehicle-owner.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="Complete Trip" onclick="return confirm('Complete this trip? Revenue will be calculated.')">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                    <h5>No Bookings Yet</h5>
                    <p class="text-muted">When customers book your rides, they will appear here.</p>
                    <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-primary">
                        Create Advertisement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection