@extends('layouts.admin')

@section('title', 'Manage Bikes - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
<<<<<<< HEAD
    <!-- Statistics Cards -->
=======
    <!-- ===== STATISTICS CARDS ===== -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Bikes</h6>
<<<<<<< HEAD
                    <h4>{{ $stats['total'] }}</h4>
=======
                    <h4>{{ $stats['total'] ?? 0 }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Available</h6>
<<<<<<< HEAD
                    <h4>{{ $stats['available'] }}</h4>
=======
                    <h4>{{ $stats['available'] ?? 0 }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Rented</h6>
<<<<<<< HEAD
                    <h4>{{ $stats['rented'] }}</h4>
=======
                    <h4>{{ $stats['rented'] ?? 0 }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Maintenance</h6>
<<<<<<< HEAD
                    <h4>{{ $stats['maintenance'] }}</h4>
=======
                    <h4>{{ $stats['maintenance'] ?? 0 }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Total Rentals</h6>
<<<<<<< HEAD
                    <h4>{{ $stats['total_rentals'] }}</h4>
=======
                    <h4>{{ $stats['total_rentals'] ?? 0 }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6>Revenue</h6>
<<<<<<< HEAD
                    <h4>MWK {{ number_format($stats['total_revenue'], 2) }}</h4>
=======
                    <h4>MWK {{ number_format($stats['total_revenue'] ?? 0, 2) }}</h4>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Manage Bikes</h4>
        <a href="{{ route('admin.bikes.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Bike
        </a>
    </div>

    <!-- Filters -->
=======
    <!-- ===== ACTION BUTTONS ===== -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2>Bikes Management</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.bikes.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Bike
            </a>
            <a href="{{ route('admin.bikes.print-labels') }}" class="btn btn-info">
                <i class="fas fa-print me-2"></i> Print QR Labels
            </a>
            <form action="{{ route('admin.bikes.bulk-generate-qr') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-qrcode me-2"></i> Generate All QR Codes
                </button>
            </form>
        </div>
    </div>

    <!-- ===== FILTERS ===== -->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
=======
                <div class="col-md-3">
                    <a href="{{ route('admin.bikes.index') }}" class="btn btn-secondary w-100">Clear Filters</a>
                </div>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
            </form>
        </div>
    </div>

<<<<<<< HEAD
    <!-- Bikes Table -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
=======
    <!-- ===== BIKES TABLE ===== -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
                        <th>Actions</th>
                    </thead>
=======
                        <th>QR Code</th>
                        <th>Actions</th>
                    </tr>
                </thead>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                <tbody>
                    @forelse($bikes as $bike)
                    <tr>
                        <td>{{ $bike->id }}</td>
<<<<<<< HEAD
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
=======
                        <td><strong>{{ $bike->bike_code ?? 'N/A' }}</strong></td>
                        <td>
                            {{ $bike->brand }} {{ $bike->model }}
                            <br>
                            <small class="text-muted">{{ $bike->color ?? 'No color' }}</small>
                        </td>
                        <td><span class="badge bg-info">{{ ucfirst($bike->type ?? 'N/A') }}</span></td>
                        <td>MWK {{ number_format($bike->price_per_hour ?? 0, 2) }}</td>
                        <td>MWK {{ number_format($bike->price_per_day ?? 0, 2) }}</td>
                        <td>MWK {{ number_format($bike->deposit_amount ?? 0, 2) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'available' => 'success',
                                    'rented' => 'warning',
                                    'maintenance' => 'danger',
                                    'inactive' => 'secondary'
                                ];
                                $statusLabels = [
                                    'available' => 'Available',
                                    'rented' => 'Rented',
                                    'maintenance' => 'Maintenance',
                                    'inactive' => 'Inactive'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$bike->status] ?? 'secondary' }}">
                                {{ $statusLabels[$bike->status] ?? ucfirst($bike->status) }}
                            </span>
                        </td>
                        <td>{{ $bike->total_rentals ?? 0 }}</td>
                        <!-- ===== QR CODE COLUMN ===== -->
                        <td>
                            @if($bike->qr_code_path)
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.bikes.preview-qr', $bike) }}" target="_blank" class="btn btn-sm btn-success" title="View QR">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bikes.download-qr', $bike) }}" class="btn btn-sm btn-info" title="Download QR">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            @else
                                <form action="{{ route('admin.bikes.generate-qr', $bike) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Generate QR">
                                        <i class="fas fa-qrcode"></i> Generate
                                    </button>
                                </form>
                            @endif
                        </td>
                        <!-- ===== ACTIONS COLUMN ===== -->
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.bikes.show', $bike) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.bikes.edit', $bike) }}" class="btn btn-sm btn-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.bikes.destroy', $bike) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this bike?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                        </td>
                    </tr>
                    @empty
                    <tr>
<<<<<<< HEAD
                        <td colspan="10" class="text-center">No bikes found.</td>
=======
                        <td colspan="11" class="text-center py-4">
                            <i class="fas fa-bicycle fa-2x d-block mb-2 text-muted"></i>
                            No bikes found. Click <a href="{{ route('admin.bikes.create') }}">Add Bike</a> to get started.
                        </td>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                    </tr>
                    @endforelse
                </tbody>
            </table>
<<<<<<< HEAD
            {{ $bikes->links() }}
=======

            <!-- ===== PAGINATION ===== -->
            <div class="d-flex justify-content-end mt-3">
                {{ $bikes->links() }}
            </div>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        </div>
    </div>
</div>
@endsection