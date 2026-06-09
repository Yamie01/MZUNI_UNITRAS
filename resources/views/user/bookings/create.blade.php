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
                    <form action="{{ route('user.bookings.store', $advertisement) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Number of Seats</label>
                            <input type="number" name="seats" class="form-control" min="1" max="{{ $advertisement->available_seats }}" required>
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
                        <div class="alert alert-info">
                            Price per seat: MWK {{ number_format($advertisement->price) }}<br>
                            Total: MWK <span id="totalPrice">{{ $advertisement->price }}</span>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Confirm Booking & Pay</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@php
    $subscription = App\Models\Subscription::where('user_id', Auth::id())
        ->where('status', 'active')
        ->where('end_date', '>', now())
        ->first();
@endphp

@if($subscription && $subscription->canBookRide())
    <div class="alert alert-info mb-3">
        <i class="fas fa-ticket-alt me-2"></i>
        <strong>Subscription Active!</strong> This ride will be FREE using your {{ $subscription->type }} pass.
        <br>
        <small>You have {{ $subscription->getRemainingTodaysRides() }} free ride(s) left today.</small>
    </div>
@endif
<script>
    document.querySelector('input[name="seats"]').addEventListener('input', function() {
        let seats = this.value;
        let pricePerSeat = {{ $advertisement->price }};
        document.getElementById('totalPrice').innerText = seats * pricePerSeat;
    });
</script>
@endsection