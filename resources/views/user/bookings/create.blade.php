@extends('layouts.app')

@section('title', 'Book Ride')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Book {{ $advertisement->from_location }} → {{ $advertisement->to_location }}</h5>
                </div>
                <div class="card-body">
                    {{-- ✅ Subscription Info Banner --}}
                    @php
                        $subscription = App\Models\Subscription::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->where('end_date', '>', now())
                            ->first();
                    @endphp

                    @if($subscription && $subscription->canBookRide())
                        <div class="alert alert-success mb-3">
                            <i class="fas fa-ticket-alt me-2"></i>
                            <strong>Free Booking!</strong> This ride is FREE with your {{ ucfirst($subscription->type) }} pass.<br>
                            <small>You have {{ $subscription->getRemainingTodaysRides() }} free ride(s) left today.</small>
                        </div>
                    @elseif($subscription && !$subscription->canBookRide())
                        <div class="alert alert-warning mb-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Daily limit reached!</strong> You've used all {{ $subscription->getDailyLimit() }} free rides today.<br>
                            <small>This ride will be charged normally.</small>
                        </div>
                    @else
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Pay per ride</strong> 
                            <a href="{{ route('subscription.index') }}" class="alert-link">Subscribe to save up to 50%!</a>
                        </div>
                    @endif

                    <form action="{{ route('user.bookings.store', $advertisement) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Number of Seats</label>
                            <input type="number" name="seats" id="seats" class="form-control" min="1" max="{{ $advertisement->available_seats }}" value="1" required>
                            <small class="text-muted">Price per seat: MWK {{ number_format($advertisement->price, 0) }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pickup Point</label>
                            <input type="text" name="pickup_point" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dropoff Point</label>
                            <input type="text" name="dropoff_point" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Requests (optional)</label>
                            <textarea name="special_requests" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="alert alert-light border" id="priceSummary">
                            <div class="d-flex justify-content-between">
                                <span>Total Amount:</span>
                                <strong id="totalPrice" class="text-primary">MWK {{ number_format($advertisement->price, 0) }}</strong>
                            </div>
                            @if($subscription && $subscription->canBookRide())
                                <div class="small text-success mt-1">
                                    <i class="fas fa-check-circle"></i> Free with your subscription!
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            @if($subscription && $subscription->canBookRide())
                                <i class="fas fa-ticket-alt me-2"></i> Confirm Free Booking
                            @else
                                <i class="fas fa-credit-card me-2"></i> Proceed to Payment
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update total price when seats change
    const seatsInput = document.getElementById('seats');
    const totalPriceSpan = document.getElementById('totalPrice');
    const pricePerSeat = {{ $advertisement->price }};
    const isSubscriptionEligible = {{ $subscription && $subscription->canBookRide() ? 'true' : 'false' }};

    seatsInput.addEventListener('input', function() {
        const seats = this.value;
        let total = pricePerSeat * seats;
        
        if (isSubscriptionEligible) {
            total = 0;
        }
        
        totalPriceSpan.innerHTML = 'MWK ' + total.toLocaleString();
    });
</script>
@endsection