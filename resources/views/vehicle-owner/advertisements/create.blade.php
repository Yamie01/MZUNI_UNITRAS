@extends('layouts.vehicle-owner')

@section('title', 'Publish a Ride - Mzuni UNITRAS')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-share-alt me-2"></i> Publish a Ride</h5>
        </div>
        <div class="card-body">
            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('vehicle-owner.advertisements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <!-- Vehicle Selection -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Select Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">Choose a vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->registration_number }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Advertisement Type -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Ride Type <span class="text-danger">*</span></label>
                        <select name="ad_type" class="form-select @error('ad_type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="ride_share" {{ old('ad_type') == 'ride_share' ? 'selected' : '' }}>Ride Share</option>
                            <option value="taxi" {{ old('ad_type') == 'taxi' ? 'selected' : '' }}>Taxi</option>
<<<<<<< HEAD
                            <option value="bus" {{ old('ad_type') == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="bike_share" {{ old('ad_type') == 'bike_share' ? 'selected' : '' }}>Bike Share</option>
=======
                            <!--<option value="bus" {{ old('ad_type') == 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="bike_share" {{ old('ad_type') == 'bike_share' ? 'selected' : '' }}>Bike Share</option>-->
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                        </select>
                        @error('ad_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Route (Road to be taken) -->
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Route <span class="text-danger">*</span></label>
                        <input type="text" name="route" id="route" class="form-control @error('route') is-invalid @enderror" 
                               value="{{ old('route') }}" placeholder="e.g., Mzuzu - Luwinga - Dunduzu" required>
                        <small class="text-muted">Enter the main road/route you will be taking</small>
                        @error('route')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- From Location -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-pin text-danger me-1"></i> Pickup Location <span class="text-danger">*</span>
                        </label>
                        <select name="from_location_id" class="form-select @error('from_location_id') is-invalid @enderror" required>
                            <option value="">Select pickup location</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('from_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_location_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- To Location -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt text-success me-1"></i> Destination <span class="text-danger">*</span>
                        </label>
                        <select name="to_location_id" class="form-select @error('to_location_id') is-invalid @enderror" required>
                            <option value="">Select destination</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
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
                        <label class="form-label fw-semibold">Departure Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="departure_time" 
                               class="form-control @error('departure_time') is-invalid @enderror"
                               value="{{ old('departure_time') }}" required>
                        @error('departure_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Price per Seat -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Price per Seat (MWK) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" 
                               class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price') }}" placeholder="0.00" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Total Seats -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Total Seats <span class="text-danger">*</span></label>
                        <input type="number" name="total_seats" 
                               class="form-control @error('total_seats') is-invalid @enderror"
                               value="{{ old('total_seats') }}" placeholder="5" required>
                        @error('total_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Available Seats -->
                    <div class="col-md-2 mb-3">
                        <label class="form-label fw-semibold">Available Seats <span class="text-danger">*</span></label>
                        <input type="number" name="available_seats" 
                               class="form-control @error('available_seats') is-invalid @enderror"
                               value="{{ old('available_seats') }}" placeholder="4" required>
                        <small class="text-muted">Cannot exceed total seats</small>
                        @error('available_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Vehicle Image Upload -->
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Vehicle Image</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <small class="text-muted">Upload an image of your vehicle (optional). Max 2MB.</small>
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-2">
                            <img id="imagePreview" src="{{ asset('images/default-car.png') }}" alt="Vehicle Preview" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-cloud-upload-alt me-1"></i> Publish Ride
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Image preview
    document.getElementById('image').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        }
        if (this.files && this.files[0]) {
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>
@endsection