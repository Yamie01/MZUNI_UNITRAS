@extends('layouts.app')

@section('title', 'Rent Bike - ' . $bike->brand . ' ' . $bike->model)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i>Rent {{ $bike->brand }} {{ $bike->model }}</h5>
                </div>
                <div class="card-body">
                    <!-- Subscription Info -->
                    @php
                        $subscription = App\Models\Subscription::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->where('end_date', '>', now())
                            ->first();
                    @endphp

                    @if($subscription && $subscription->canBookRide())
                        <div class="alert alert-success">
                            <i class="fas fa-ticket-alt me-2"></i>
                            <strong>Free Rental!</strong> This rental is FREE with your {{ ucfirst($subscription->type) }} pass.<br>
                            <small>You have {{ $subscription->getRemainingTodaysRides() }} free ride(s) left today.</small>
                        </div>
                    @endif

                    <!-- Bike Info -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-6">
                                <strong>Hourly Rate:</strong> MWK {{ number_format($bike->price_per_hour, 0) }}
                            </div>
                            <div class="col-6">
                                <strong>Daily Rate:</strong> MWK {{ number_format($bike->price_per_day, 0) }}
                            </div>
                        </div>
                        <div class="mt-2">
                            <strong>Deposit:</strong> MWK {{ number_format($bike->deposit_amount, 0) }} (refundable)
                        </div>
                    </div>

                    <form action="{{ route('user.bikes.rent.process', $bike) }}" method="POST" id="rentalForm">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Duration *</label>
                                <input type="number" name="duration" id="duration" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Duration Type *</label>
                                <select name="duration_type" id="duration_type" class="form-select" required>
                                    <option value="hour">Hour(s)</option>
                                    <option value="day">Day(s)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pickup Location *</label>
                            <input type="text" name="pickup_location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dropoff Location *</label>
                            <input type="text" name="dropoff_location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="alert alert-warning small">
                            <strong>Terms:</strong> Deposit refundable upon return with no damages.
                        </div>

                        <div class="alert alert-success text-center" id="pricePreview">
                            <strong>Total Amount: MWK <span id="totalAmount">0</span></strong>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1" id="submitBtn">
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
    const durationInput = document.getElementById('duration');
    const durationTypeSelect = document.getElementById('duration_type');
    const totalAmountSpan = document.getElementById('totalAmount');
    const hourlyRate = {{ $bike->price_per_hour }};
    const dailyRate = {{ $bike->price_per_day }};
    const hasSubscription = {{ $subscription && $subscription->canBookRide() ? 'true' : 'false' }};

    function calculateTotal() {
        const duration = parseInt(durationInput.value) || 0;
        const durationType = durationTypeSelect.value;
        
        let total = 0;
        if (durationType === 'hour') {
            total = duration * hourlyRate;
        } else {
            total = duration * dailyRate;
        }
        
        if (hasSubscription) {
            total = 0;
        }
        
        totalAmountSpan.textContent = total.toLocaleString();
    }

    durationInput.addEventListener('input', calculateTotal);
    durationTypeSelect.addEventListener('change', calculateTotal);
    calculateTotal();

    const form = document.getElementById('rentalForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
    });
</script>
@endsection