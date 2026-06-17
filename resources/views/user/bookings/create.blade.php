@extends('layouts.app')

@section('title', 'Book Ride')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>
                        Book Ride: 
                        {{ $advertisement->fromLocation->name ?? $advertisement->from_location }} 
                        → 
                        {{ $advertisement->toLocation->name ?? $advertisement->to_location }}
                    </h5>
                </div>
                <div class="card-body">
                    {{-- ✅ Subscription Info Banner --}}
                    @php
                        $subscription = App\Models\Subscription::where('user_id', Auth::id())
                            ->where('status', 'active')
                            ->where('end_date', '>', now())
                            ->first();
                        $canBookFree = $subscription && $subscription->canBookRide();
                    @endphp

                    @if($canBookFree)
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

                        {{-- Location Dropdowns (from/to) --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="from_location_id" class="form-label">
                                    <i class="fas fa-map-pin text-danger"></i> Pickup Location *
                                </label>
                                <select name="from_location_id" id="from_location_id" class="form-select" required>
                                    <option value="">Select pickup</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" 
                                            {{ old('from_location_id', $advertisement->from_location_id) == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="to_location_id" class="form-label">
                                    <i class="fas fa-map-marker-alt text-success"></i> Destination *
                                </label>
                                <select name="to_location_id" id="to_location_id" class="form-select" required>
                                    <option value="">Select destination</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" 
                                            {{ old('to_location_id', $advertisement->to_location_id) == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Seats and Date/Time --}}
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="seats" class="form-label">
                                    <i class="fas fa-chair"></i> Number of Seats *
                                </label>
                                <input type="number" name="seats" id="seats" class="form-control" 
                                       min="1" max="{{ $advertisement->available_seats }}" 
                                       value="{{ old('seats', 1) }}" required>
                                <small class="text-muted">
                                    Max {{ $advertisement->available_seats }} seats available
                                </small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="departure_date" class="form-label">
                                    <i class="fas fa-calendar-day"></i> Date
                                </label>
                                <input type="date" name="departure_date" id="departure_date" class="form-control" 
                                       value="{{ old('departure_date', \Carbon\Carbon::parse($advertisement->departure_time)->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="departure_time" class="form-label">
                                    <i class="fas fa-clock"></i> Time
                                </label>
                                <input type="time" name="departure_time" id="departure_time" class="form-control" 
                                       value="{{ old('departure_time', \Carbon\Carbon::parse($advertisement->departure_time)->format('H:i')) }}" required>
                            </div>
                        </div>

                        {{-- Special Requests --}}
                        <div class="mb-3">
                            <label for="special_requests" class="form-label">
                                <i class="fas fa-comment"></i> Special Requests (optional)
                            </label>
                            <textarea name="special_requests" id="special_requests" class="form-control" rows="2">{{ old('special_requests') }}</textarea>
                        </div>

                        {{-- Price Summary --}}
                        <div class="alert alert-light border" id="priceSummary">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><strong>Total Amount:</strong></span>
                                <span>
                                    <span id="totalPrice" class="fw-bold text-primary">
                                        MWK {{ number_format($advertisement->price, 0) }}
                                    </span>
                                    @if($canBookFree)
                                        <span class="badge bg-success ms-2">FREE</span>
                                    @endif
                                </span>
                            </div>
                            @if($canBookFree)
                                <div class="small text-success mt-1">
                                    <i class="fas fa-check-circle"></i> Free with your subscription!
                                </div>
                            @endif
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary w-100">
                            @if($canBookFree)
                                <i class="fas fa-ticket-alt me-2"></i> Confirm Free Booking
                            @else
                                <i class="fas fa-credit-card me-2"></i> Proceed to Payment
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            {{-- Ride Details Sidebar --}}
            <div class="card mt-4 bg-light">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle text-primary"></i> Ride Details</h6>
                    <hr>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">From</dt>
                        <dd class="col-sm-8">{{ $advertisement->fromLocation->name ?? $advertisement->from_location }}</dd>
                        <dt class="col-sm-4">To</dt>
                        <dd class="col-sm-8">{{ $advertisement->toLocation->name ?? $advertisement->to_location }}</dd>
                        <dt class="col-sm-4">Departs</dt>
                        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($advertisement->departure_time)->format('d M Y, H:i') }}</dd>
                        <dt class="col-sm-4">Seats left</dt>
                        <dd class="col-sm-8">{{ $advertisement->available_seats }}</dd>
                        <dt class="col-sm-4">Price per seat</dt>
                        <dd class="col-sm-8">MWK {{ number_format($advertisement->price, 0) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const seatsInput = document.getElementById('seats');
        const totalPriceSpan = document.getElementById('totalPrice');
        const pricePerSeat = {{ $advertisement->price }};
        const isFree = {{ $canBookFree ? 'true' : 'false' }};

        function updatePrice() {
            const seats = parseInt(seatsInput.value) || 0;
            let total = pricePerSeat * seats;
            if (isFree) {
                total = 0;
            }
            totalPriceSpan.textContent = 'MWK ' + total.toLocaleString();
            // If free, add badge
            if (isFree) {
                totalPriceSpan.innerHTML = 'MWK 0 <span class="badge bg-success ms-2">FREE</span>';
            }
        }

        seatsInput.addEventListener('input', updatePrice);
        updatePrice(); // initial
    });
</script>
@endpush
@endsection