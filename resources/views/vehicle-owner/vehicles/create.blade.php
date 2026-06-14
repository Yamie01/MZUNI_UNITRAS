@extends('layouts.vehicle-owner')

@section('title', 'Add Vehicle - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Add New Vehicle</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicle-owner.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="car">Car</option>
                            <option value="taxi">Taxi</option>
                            <option value="bus">Bus</option>
                            <option value="minibus">Minibus</option>
                            <option value="bike">Bike</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registration Number *</label>
                        <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Make</label>
                        <input type="text" name="make" class="form-control" value="{{ old('make') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control" value="{{ old('model') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ old('year') }}" min="1900" max="{{ date('Y') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="{{ old('color') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Capacity (seats) *</label>
                        <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select">
                            <option value="">Select</option>
                            <option value="petrol">Petrol</option>
                            <option value="diesel">Diesel</option>
                            <option value="electric">Electric</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per seat (MWK)</label>
                        <input type="number" step="0.01" name="price_per_seat" class="form-control" value="{{ old('price_per_seat') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Day (MWK)</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control" value="{{ old('price_per_day') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Insurance Number</label>
                        <input type="text" name="insurance_number" class="form-control" value="{{ old('insurance_number') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Insurance Expiry</label>
                        <input type="date" name="insurance_expiry" class="form-control" value="{{ old('insurance_expiry') }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('vehicle-owner.vehicles.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Add Vehicle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection