@extends('vehicle-owner.layouts.owner')

@section('title', 'Edit Advertisement')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-edit text-primary me-2"></i>Edit Advertisement</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('vehicle-owner.advertisements.update', $advertisement) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Vehicle Selection -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Vehicle *</label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">Choose a vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $advertisement->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->model }} ({{ $vehicle->registration_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Advertisement Type -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Advertisement Type *</label>
                        <select name="ad_type" class="form-select @error('ad_type') is-invalid @enderror" required>
                            <option value="ride_share" {{ old('ad_type', $advertisement->ad_type) == 'ride_share' ? 'selected' : '' }}>Ride Share</option>
                            <option value="taxi" {{ old('ad_type', $advertisement->ad_type) == 'taxi' ? 'selected' : '' }}>Taxi</option>
                            <option value="bus" {{ old('ad_type', $advertisement->ad_type) == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="bike_share" {{ old('ad_type', $advertisement->ad_type) == 'bike_share' ? 'selected' : '' }}>Bike Share</option>
                        </select>
                        @error('ad_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Title -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title', $advertisement->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="4" required>{{ old('description', $advertisement->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- From & To Locations -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-map-pin text-danger"></i> Pickup Location *</label>
                        <select name="from_location_id" class="form-select @error('from_location_id') is-invalid @enderror" required>
                            <option value="">Select pickup</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('from_location_id', $advertisement->from_location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fas fa-map-marker-alt text-success"></i> Destination *</label>
                        <select name="to_location_id" class="form-select @error('to_location_id') is-invalid @enderror" required>
                            <option value="">Select destination</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('to_location_id', $advertisement->to_location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Departure Date & Time -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Departure Date & Time *</label>
                        <input type="datetime-local" name="departure_time" 
                               class="form-control @error('departure_time') is-invalid @enderror"
                               value="{{ old('departure_time', \Carbon\Carbon::parse($advertisement->departure_time)->format('Y-m-d\TH:i')) }}" required>
                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price per Seat -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Seat (MWK) *</label>
                        <input type="number" step="0.01" name="price" 
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $advertisement->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Total Seats -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Seats *</label>
                        <input type="number" name="total_seats" 
                               class="form-control @error('total_seats') is-invalid @enderror"
                               value="{{ old('total_seats', $advertisement->total_seats) }}" required>
                        @error('total_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Available Seats -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Available Seats *</label>
                        <input type="number" name="available_seats" 
                               class="form-control @error('available_seats') is-invalid @enderror"
                               value="{{ old('available_seats', $advertisement->available_seats) }}" required>
                        <small class="text-muted">Cannot exceed total seats</small>
                        @error('available_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-end mt-3">
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection