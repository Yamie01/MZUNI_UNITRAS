@extends('layouts.vehicle-owner') {{-- adjust to your layout --}}

@section('title', 'My Vehicles - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Vehicles</h6>
                    <h4>{{ $stats['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Approved</h6>
                    <h4>{{ $stats['approved'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h4>{{ $stats['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Available</h6>
                    <h4>{{ $stats['available'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Vehicles</h4>
        <a href="{{ route('vehicle-owner.vehicles.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add Vehicle
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vehicle-owner.vehicles.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by reg or model" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="approved" class="form-select">
                        <option value="">All</option>
                        <option value="yes" {{ request('approved') == 'yes' ? 'selected' : '' }}>Approved</option>
                        <option value="no" {{ request('approved') == 'no' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Vehicles Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Reg No.</th>
                        <th>Model</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Approved</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->registration_number }}</td>
                        <td>{{ $vehicle->model }}</td>
                        <td>{{ ucfirst($vehicle->vehicle_type) }}</td>
                        <td>{{ $vehicle->capacity }}</td>
                        <td>
                            <span class="badge bg-{{ $vehicle->status == 'available' ? 'success' : 'warning' }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </td>
                        <td>
                            @if($vehicle->is_approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                                @if($vehicle->rejection_reason)
                                    <i class="fas fa-info-circle text-danger" data-bs-toggle="tooltip" title="{{ $vehicle->rejection_reason }}"></i>
                                @endif
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vehicle-owner.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('vehicle-owner.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this vehicle?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @if($vehicle->is_approved)
                                <form action="{{ route('vehicle-owner.vehicles.toggle-status', $vehicle) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">
                                        {{ $vehicle->status == 'available' ? 'Set Maintenance' : 'Set Available' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No vehicles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $vehicles->links() }}
        </div>
    </div>
</div>
@endsection