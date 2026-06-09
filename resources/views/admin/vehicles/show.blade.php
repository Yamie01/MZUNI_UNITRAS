@extends('layouts.admin')

@section('title', 'Vehicle Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Vehicle Details: {{ $vehicle->registration_number }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Registration:</strong> {{ $vehicle->registration_number }}</p>
                    <p><strong>Model:</strong> {{ $vehicle->model }}</p>
                    <p><strong>Make:</strong> {{ $vehicle->make ?? 'N/A' }}</p>
                    <p><strong>Year:</strong> {{ $vehicle->year ?? 'N/A' }}</p>
                    <p><strong>Color:</strong> {{ $vehicle->color ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($vehicle->vehicle_type) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Capacity:</strong> {{ $vehicle->capacity }} seats</p>
                    <p><strong>Fuel Type:</strong> {{ ucfirst($vehicle->fuel_type ?? 'N/A') }}</p>
                    <p><strong>Price per KM:</strong> MWK {{ number_format($vehicle->price_per_km, 2) ?? 'N/A' }}</p>
                    <p><strong>Price per Day:</strong> MWK {{ number_format($vehicle->price_per_day, 2) ?? 'N/A' }}</p>
                    <p><strong>Insurance:</strong> {{ $vehicle->insurance_number ?? 'N/A' }} (expires {{ $vehicle->insurance_expiry ? $vehicle->insurance_expiry->format('d M Y') : 'N/A' }})</p>
                    <p><strong>Approved:</strong> 
                        @if($vehicle->is_approved)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-warning">No</span>
                        @endif
                    </p>
                </div>
            </div>
            @if($vehicle->description)
            <div class="row mt-3">
                <div class="col-12">
                    <p><strong>Description:</strong> {{ $vehicle->description }}</p>
                </div>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection