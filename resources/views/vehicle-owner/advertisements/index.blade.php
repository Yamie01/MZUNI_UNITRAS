@extends('layouts.vehicle-owner')

@section('title', 'My Advertisements - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total</h6>
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
                    <h6>Approved</h6>
                    <h4>{{ $stats['approved'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h4>{{ $stats['rejected'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Active</h6>
                    <h4>{{ $stats['active'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h6>Expired</h6>
                    <h4>{{ $stats['expired'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Advertisements</h4>
        <a href="{{ route('vehicle-owner.advertisements.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> New Advertisement
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vehicle-owner.advertisements.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by title or route" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="ride_share" {{ request('type') == 'ride_share' ? 'selected' : '' }}>Ride Share</option>
                        <option value="taxi" {{ request('type') == 'taxi' ? 'selected' : '' }}>Taxi</option>
                        <option value="bus" {{ request('type') == 'bus' ? 'selected' : '' }}>Bus</option>
                        <option value="bike_share" {{ request('type') == 'bike_share' ? 'selected' : '' }}>Bike Share</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Advertisements Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Vehicle</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Price</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advertisements as $ad)
                    <tr>
                        <td>{{ Str::limit($ad->title, 30) }}</td>
                        <td>{{ $ad->vehicle->registration_number ?? 'N/A' }}</td>
                        <td>{{ $ad->from_location }} → {{ $ad->to_location }}</td>
                        <td>{{ $ad->departure_time->format('d M Y H:i') }}</td>
                        <td>MWK {{ number_format($ad->price, 2) }}</td>
                        <td>{{ $ad->available_seats }}/{{ $ad->total_seats }}</td>
                        <td>
                            @if($ad->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($ad->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($ad->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @if($ad->rejection_reason)
                                    <i class="fas fa-info-circle text-danger" data-bs-toggle="tooltip" title="{{ $ad->rejection_reason }}"></i>
                                @endif
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('vehicle-owner.advertisements.show', $ad) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($ad->status != 'approved' || !$ad->bookings()->exists())
                                <a href="{{ route('vehicle-owner.advertisements.edit', $ad) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                            @if($ad->status == 'approved' && !$ad->bookings()->exists())
                                <form action="{{ route('vehicle-owner.advertisements.duplicate', $ad) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Duplicate">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('vehicle-owner.advertisements.destroy', $ad) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this advertisement?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No advertisements found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $advertisements->links() }}
        </div>
    </div>
</div>
@endsection