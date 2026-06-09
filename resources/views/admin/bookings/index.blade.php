@extends('layouts.admin')

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
                    <h6>Completed</h6>
                    <h4>{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Cancelled</h6>
                    <h4>{{ $stats['cancelled'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6>Revenue</h6>
                    <h4>MWK {{ number_format($stats['total_revenue'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search by ID or customer" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}" placeholder="From Date">
                </div>
                <div class="col-md-2">
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}" placeholder="To Date">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.bookings.export') }}" class="btn btn-success w-100">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5>All Bookings</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Ride</th>
                            <th>Route</th>
                            <th>Seats</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                            <th>Actions</th>
                            <th>Trip Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>
                                <strong>{{ $booking->user->name ?? 'N/A' }}</strong><br>
                                <small class="text-muted">{{ $booking->user->email ?? 'N/A' }}</small>
                            </td>
                            <td>{{ Str::limit($booking->advertisement->title ?? 'N/A', 30) }}</td>
                            <td>
                                {{ $booking->advertisement->from_location ?? 'N/A' }} → 
                                {{ $booking->advertisement->to_location ?? 'N/A' }}
                            </td>
                            <td>{{ $booking->number_of_seats }}</td>
                            <td>MWK {{ number_format($booking->total_price, 2) }}</td>
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
                            <td>{{ $booking->created_at->format('d M Y H:i') }}</td>
                            
                            <td>
                                @if($booking->trip_status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                    <br><small>{{ $booking->trip_completed_at->format('d M H:i') }}</small>
                                @elseif($booking->trip_status == 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @else
                                    <span class="badge bg-secondary">Scheduled</span>
                                @endif
                            </td>
                            <td>
                               <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-info">                                   <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this booking?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No bookings found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection