@extends('layouts.vehicle-owner')

@section('title', 'Edit Vehicle - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Edit Vehicle: {{ $vehicle->registration_number }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicle-owner.vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <!-- same fields as create, with old values -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" class="form-select" required>
                            <option value="car" {{ old('vehicle_type', $vehicle->vehicle_type) == 'car' ? 'selected' : '' }}>Car</option>
                            <option value="taxi" {{ old('vehicle_type', $vehicle->vehicle_type) == 'taxi' ? 'selected' : '' }}>Taxi</option>
                            <option value="bus" {{ old('vehicle_type', $vehicle->vehicle_type) == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="minibus" {{ old('vehicle_type', $vehicle->vehicle_type) == 'minibus' ? 'selected' : '' }}>Minibus</option>
                            <option value="bike" {{ old('vehicle_type', $vehicle->vehicle_type) == 'bike' ? 'selected' : '' }}>Bike</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registration Number *</label>
                        <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $vehicle->registration_number) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Make</label>
                        <input type="text" name="make" class="form-control" value="{{ old('make', $vehicle->make) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model) }}" required>
                    </div>
                    <!-- ... continue with other fields ... -->
                </div>
                <div class="text-end">
                    <a href="{{ route('vehicle-owner.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection