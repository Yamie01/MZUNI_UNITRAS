@extends('layouts.vehicle-owner')

@section('title', 'Add Vehicle - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-car me-2"></i>Add New Vehicle</h5>
        </div>
        <div class="card-body">
            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                After submitting, your vehicle will be reviewed by an admin. You can publish rides once approved.
            </div>

            <form action="{{ route('vehicle-owner.vehicles.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    {{-- Vehicle Type --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Vehicle Type *</label>
                        <select name="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>
                            <option value="">Select</option>
                            <option value="car" {{ old('vehicle_type') == 'car' ? 'selected' : '' }}>Car</option>
                            <option value="taxi" {{ old('vehicle_type') == 'taxi' ? 'selected' : '' }}>Taxi</option>
                            <option value="bus" {{ old('vehicle_type') == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="minibus" {{ old('vehicle_type') == 'minibus' ? 'selected' : '' }}>Minibus</option>
                            <option value="bike" {{ old('vehicle_type') == 'bike' ? 'selected' : '' }}>Bike</option>
                            <option value="coaster" {{ old('vehicle_type') == 'coaster' ? 'selected' : '' }}>Coaster</option>
                        </select>
                        @error('vehicle_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Registration Number --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Registration Number *</label>
                        <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" 
                               value="{{ old('registration_number') }}" required>
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Make --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Make</label>
                        <input type="text" name="make" class="form-control @error('make') is-invalid @enderror" 
                               value="{{ old('make') }}">
                        @error('make')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Model --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                               value="{{ old('model') }}" required>
                        @error('model')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Year --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" 
                               value="{{ old('year') }}" min="1900" max="{{ date('Y') }}">
                        @error('year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Color --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" 
                               value="{{ old('color') }}">
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Capacity --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Capacity (seats) *</label>
                        <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror" 
                               value="{{ old('capacity') }}" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Fuel Type --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fuel Type</label>
                        <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror">
                            <option value="">Select</option>
                            <option value="petrol" {{ old('fuel_type') == 'petrol' ? 'selected' : '' }}>Petrol</option>
                            <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                            <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                        @error('fuel_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price per seat --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per seat (MWK)</label>
                        <input type="number" step="0.01" name="price_per_seat" class="form-control @error('price_per_seat') is-invalid @enderror" 
                               value="{{ old('price_per_seat') }}">
                        @error('price_per_seat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price per Day --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Day (MWK)</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control @error('price_per_day') is-invalid @enderror" 
                               value="{{ old('price_per_day') }}">
                        @error('price_per_day')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Insurance Number --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Insurance Number</label>
                        <input type="text" name="insurance_number" class="form-control @error('insurance_number') is-invalid @enderror" 
                               value="{{ old('insurance_number') }}">
                        @error('insurance_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Insurance Expiry --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Insurance Expiry</label>
                        <input type="date" name="insurance_expiry" class="form-control @error('insurance_expiry') is-invalid @enderror" 
                               value="{{ old('insurance_expiry') }}">
                        @error('insurance_expiry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Images --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control @error('images.*') is-invalid @enderror" 
                               multiple accept="image/*">
                        <small class="text-muted">You can select multiple images (JPEG, PNG, JPG, GIF) - Max 2MB each</small>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="text-end mt-3">
                    <a href="{{ route('vehicle-owner.vehicles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Add Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection