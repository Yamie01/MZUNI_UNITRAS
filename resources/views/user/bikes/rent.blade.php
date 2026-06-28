@extends('layouts.app')

@section('title', 'Activate Bike - ' . $bike->brand . ' ' . $bike->model)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i>Activate Bike</h5>
                </div>
                <div class="card-body">

                    <!-- ============================================================
                    DISPLAY ERROR MESSAGES
                    ============================================================ -->
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {!! session('error') !!}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Check if user has active rental -->
                    @php
                        $activeRental = App\Models\BikeRental::where('user_id', auth()->id())
                            ->where('status', 'active')
                            ->first();
                    @endphp

                    @if($activeRental)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>⚠️ Active Rental Detected!</strong><br>
                            You already have an active bike rental ({{ $activeRental->rental_code }}).<br>
                            Please <a href="{{ route('user.bike-rentals.show', $activeRental) }}" class="alert-link">return the bike</a> before starting a new rental.
                        </div>
                    @endif

                    <!-- Bike Info -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-6">
                                <strong>Type:</strong> {{ ucfirst($bike->type) }}
                            </div>
                            <div class="col-6">
                                <strong>Rate:</strong> MWK 2/minute
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>Brand:</strong> {{ $bike->brand }}
                            </div>
                            <div class="col-6">
                                <strong>Model:</strong> {{ $bike->model }}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <strong>Status:</strong> 
                                <span class="badge bg-success">Available</span>
                            </div>
                            <div class="col-6">
                                <strong>Bike Code:</strong> {{ $bike->bike_code ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Activation Form -->
                    <form action="{{ route('user.bikes.rent.process', $bike) }}" method="POST" id="rentalForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-id-card me-1"></i> 
                                @if(auth()->user()->user_type === 'student')
                                    University Registration Number
                                @elseif(auth()->user()->user_type === 'staff')
                                    Staff ID / Employment Number
                                @else
                                    Identification Number
                                @endif
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="registration_number" id="registration_number" 
                                   class="form-control @error('registration_number') is-invalid @enderror" 
                                   placeholder="e.g., MZUNI/2023/12345" value="{{ old('registration_number') }}" required>
                            @error('registration_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the ID you used when registering.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-phone me-1"></i> Phone Number <span class="text-danger">*</span>
                            </label>
                            <input type="tel" name="phone_number" id="phone_number" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   placeholder="0990179811" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the phone number you used when registering.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-pin me-1"></i> Pickup Location <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="pickup_location" id="pickup_location" 
                                   class="form-control @error('pickup_location') is-invalid @enderror" 
                                   list="locations" placeholder="Where are you picking the bike?" value="{{ old('pickup_location', 'Mzuzu campus') }}" required>
                            <datalist id="locations">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                                @endforeach
                            </datalist>
                            @error('pickup_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-flag-checkered me-1"></i> Dropoff Location <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="dropoff_location" id="dropoff_location" 
                                   class="form-control @error('dropoff_location') is-invalid @enderror" 
                                   list="locations" placeholder="Where will you return the bike?" value="{{ old('dropoff_location', 'Library') }}" required>
                            <datalist id="locations">
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                                @endforeach
                            </datalist>
                            @error('dropoff_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Terms -->
                        <div class="alert alert-warning small">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>How it works:</strong><br>
                            • The bike will be activated immediately.<br>
                            • You will be charged <strong>MWK 2 per minute</strong> of usage.<br>
                            • The timer starts when you activate the bike.<br>
                            • Payment is due when you return the bike.<br>
                            • Total cost = Minutes used × MWK 2
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-play-circle me-2"></i> Activate Bike
                            </button>
                            <a href="{{ route('user.bikes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('rentalForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Activating...';
            });
        }
    });
</script>
@endsection