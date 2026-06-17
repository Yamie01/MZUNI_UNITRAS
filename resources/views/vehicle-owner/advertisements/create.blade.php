@extends('layouts.vehicle-owner')

@section('title', 'Publish a Ride - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-share-alt text-primary"></i> Publish a Ride</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicle-owner.advertisements.store') }}" method="POST">
                @csrf

                <div class="row">
                    <!-- Vehicle Selection -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Vehicle *</label>
                        <select name="vehicle_id" class="form-select" required>
                            <option value="">Choose a vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->model }} ({{ $vehicle->registration_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Advertisement Type -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Advertisement Type *</label>
                        <select name="ad_type" class="form-select" required>
                            <option value="ride_share" {{ old('ad_type') == 'ride_share' ? 'selected' : '' }}>Ride Share</option>
                            <option value="taxi" {{ old('ad_type') == 'taxi' ? 'selected' : '' }}>Taxi</option>
                            <option value="bus" {{ old('ad_type') == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="bike_share" {{ old('ad_type') == 'bike_share' ? 'selected' : '' }}>Bike Share</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    </div>

                    <!-- From & To Locations (Dropdowns) -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-map-pin text-danger"></i> Pickup Location *</label>
                        <select name="from_location_id" class="form-select" required>
                            <option value="">Select pickup</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('from_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-map-marker-alt text-success"></i> Destination *</label>
                        <select name="to_location_id" class="form-select" required>
                            <option value="">Select destination</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Departure Date & Time -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Departure Date & Time *</label>
                        <input type="datetime-local" name="departure_time" class="form-control"
                               value="{{ old('departure_time') }}" required>
                    </div>

                    <!-- Price per Seat -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Seat (MWK) *</label>
                        <input type="number" step="0.01" name="price" class="form-control"
                               value="{{ old('price') }}" required>
                    </div>

                    <!-- Total Seats -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Seats *</label>
                        <input type="number" name="total_seats" class="form-control"
                               value="{{ old('total_seats') }}" required>
                    </div>

                    <!-- Available Seats -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Available Seats *</label>
                        <input type="number" name="available_seats" class="form-control"
                               value="{{ old('available_seats') }}" required>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-end mt-3">
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-cloud-upload-alt"></i> Publish Ride</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection