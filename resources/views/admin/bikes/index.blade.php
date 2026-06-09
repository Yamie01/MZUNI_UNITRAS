@extends('layouts.admin')

@section('title', 'Manage Bikes - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Bikes</h6>
                    <h4>{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Available</h6>
                    <h4>{{ $stats['available'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Rented</h6>
                    <h4>{{ $stats['rented'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Maintenance</h6>
                    <h4>{{ $stats['maintenance'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Total Rentals</h6>
                    <h4>{{ $stats['total_rentals'] }}</h4>
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manage Bikes</h4>
        <a href="{{ route('admin.bikes.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Bike
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search bike code, brand..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="rented" {{ request('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="mountain" {{ request('type') == 'mountain' ? 'selected' : '' }}>Mountain</option>
                        <option value="road" {{ request('type') == 'road' ? 'selected' : '' }}>Road</option>
                        <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="electric" {{ request('type') == 'electric' ? 'selected' : '' }}>Electric</option>
                        <option value="city" {{ request('type') == 'city' ? 'selected' : '' }}>City</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bikes Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bike Code</th>
                        <th>Brand/Model</th>
                        <th>Type</th>
                        <th>Price/Hour</th>
                        <th>Price/Day</th>
                        <th>Deposit</th>
                        <th>Status</th>
                        <th>Rentals</th>
                        <th>Actions</th>
                    </thead>
                <tbody>
                    @forelse($bikes as $bike)
                    <tr>
                        <td>{{ $bike->id }}</td>
                        <td><strong>{{ $bike->bike_code }}</strong></td>
                        <td>
                            {{ $bike->brand }} {{ $bike->model }}<br>
                            <small class="text-muted">{{ $bike->color ?? 'No color' }}</small>
                        </td>
                        <td><span class="badge bg-info">{{ ucfirst($bike->type) }}</span></td>
                        <td>MWK {{ number_format($bike->price_per_hour, 2) }}</td>
                        <td>MWK {{ number_format($bike->price_per_day, 2) }}</td>
                        <td>MWK {{ number_format($bike->deposit_amount, 2) }}</td>
                        <td>
                            @if($bike->status == 'available')
                                <span class="badge bg-success">Available</span>
                            @elseif($bike->status == 'rented')
                                <span class="badge bg-warning">Rented</span>
                            @else
                                <span class="badge bg-danger">{{ ucfirst($bike->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $bike->total_rentals }}</td>
                        <td>
                            <a href="{{ route('admin.bikes.show', $bike) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.bikes.edit', $bike) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <span class="badge bg-{{ $bike->status == 'available' ? 'success' : ($bike->status == 'rented' ? 'warning' : 'danger') }}">
                                {{ ucfirst($bike->status) }}
                            </span>
                            <form action="{{ route('admin.bikes.destroy', $bike) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this bike?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">No bikes found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $bikes->links() }}
        </div>
    </div>
</div>
@endsection