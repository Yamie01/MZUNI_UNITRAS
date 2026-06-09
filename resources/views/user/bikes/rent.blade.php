@extends('layouts.app')

@section('title', 'Rent Bike - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Rent Bike: {{ $bike->brand }} {{ $bike->model }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Bike Code:</strong> {{ $bike->bike_code }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($bike->type) }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-success">Available</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Hourly Rate:</strong> MWK {{ number_format($bike->price_per_hour, 2) }}</p>
                            <p><strong>Daily Rate:</strong> MWK {{ number_format($bike->price_per_day, 2) }}</p>
                            <p><strong>Deposit:</strong> MWK {{ number_format($bike->deposit_amount, 2) }}</p>
                        </div>
                    </div>

                    <form action="{{ route('user.bikes.process-rent', $bike) }}" method="POST" id="rentalForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rental Type *</label>
                                <select name="duration_type" id="duration_type" class="form-select" required>
                                    <option value="hour">Hourly Rental</option>
                                    <option value="day">Daily Rental</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration *</label>
                                <input type="number" name="duration" id="duration" class="form-control" min="1" max="24" required>
                                <small class="text-muted">Maximum 24 hours / 1 day</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Start Date & Time *</label>
                            <input type="datetime-local" name="start_time" class="form-control" value="{{ old('start_time', now()->format('Y-m-d\TH:i')) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Pickup Location *</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="e.g., Main Campus, City Campus" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Dropoff Location *</label>
                            <input type="text" name="dropoff_location" class="form-control" placeholder="e.g., Main Campus, City Campus" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any special requests or instructions?"></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>Rental Terms:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Security deposit is refundable upon return with no damages</li>
                                <li>Please return the bike on time to avoid extra charges</li>
                                <li>Damages will be assessed and charged accordingly</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-credit-card me-2"></i> Proceed to Payment
                            </button>
                            <a href="{{ route('user.bikes.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Prevent double submission
    document.getElementById('rentalForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
    });
</script>
@endsection