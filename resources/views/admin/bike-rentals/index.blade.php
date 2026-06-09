@extends('layouts.admin')

@section('title', 'Bike Rentals')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Bike Rentals Management</h5>
        </div>
        <div class="card-body">
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="alert alert-info">Total: {{ $stats['total'] }}</div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-success">Active: {{ $stats['active'] }}</div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-secondary">Completed: {{ $stats['completed'] }}</div>
                </div>
                <div class="col-md-3">
                    <div class="alert alert-warning">Revenue: MWK {{ number_format($stats['total_revenue'], 2) }}</div>
                </div>
            </div>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Rental Code</th>
                        <th>Bike</th>
                        <th>Renter</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Rental Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                    <tr>
                        <td>{{ $rental->rental_code }}</td>
                        <td>{{ $rental->bike->brand }} {{ $rental->bike->model }}</td>
                        <td>{{ $rental->user->name }}</td>
                        <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                        <td>MWK {{ number_format($rental->total_amount, 2) }}</td>
                        <td>
                            @if($rental->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($rental->status === 'completed')
                                <span class="badge bg-info">Completed</span>
                            @elseif($rental->status === 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                         </td>
                        <td>{{ $rental->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.bike-rentals.show', $rental) }}" class="btn btn-sm btn-primary">View</a>
                            @if($rental->status === 'active')
                                <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">Track</a>
                            @endif
                            @if($rental->status === 'pending')
                                <button class="btn btn-sm btn-danger" onclick="alert('Manual payment verification required')">Verify</button>
                            @endif
                         </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center">No bike rentals found</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $rentals->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

<!--@extends('layouts.admin')

@section('title', 'Bike Rentals - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">Statistics Cards
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Rentals</h6>
                    <h4>{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Active Rentals</h6>
                    <h4>{{ $stats['active'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Completed</h6>
                    <h4>{{ $stats['completed'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Total Revenue</h6>
                    <h4>MWK {{ number_format($stats['total_revenue'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Bike Rentals</h4>
    </div>

    <!-- Filters 
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>
    @if($rental->status === 'active')
    <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">
        <i class="fas fa-map-marked-alt"></i> Track
    </a>
@endif
    <!-- Rentals Table 
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                     <tr>
                        <th>ID</th>
                        <th>Rental Code</th>
                        <th>Bike</th>
                        <th>User</th>
                        <th>Start Time</th>
                        <th>Duration</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                     </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                    <tr>
                        <td>#{{ $rental->id }}</td>
                        <td>{{ $rental->rental_code }}</td>
                        <td>{{ $rental->bike->bike_code }}<br><small>{{ $rental->bike->brand }} {{ $rental->bike->model }}</small></td>
                        <td>{{ $rental->user->name }}</td>
                        <td>{{ $rental->start_time->format('d M Y H:i') }}</td>
                        <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                        <td>MWK {{ number_format($rental->total_amount, 2) }}</td>
                        <td>
                            @if($rental->status == 'active')
                                <span class="badge bg-warning">Active</span>
                            @elseif($rental->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($rental->status == 'pending')
                                <span class="badge bg-info">Pending</span>
                            @elseif($rental->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.bike-rentals.show', $rental) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($rental->status == 'pending')
                                <form action="{{ route('admin.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this rental?')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No rentals found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $rentals->links() }}
        </div>
    </div>
</div>
@endsection