@extends('vehicle-owner.layouts.owner')

@section('title', 'Booking Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Booking Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                <p><strong>Customer:</strong> {{ $booking->user->name ?? 'N/A' }}</p>
                <p><strong>Route:</strong> {{ $booking->pickup_point }} → {{ $booking->dropoff_point }}</p>
                <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                <p><strong>Total Price:</strong> MWK {{ number_format($booking->total_price, 2) }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong>
                    @if($booking->status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($booking->status === 'confirmed')
                        <span class="badge bg-info">Confirmed</span>
                    @elseif($booking->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @elseif($booking->status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                    @endif
                </p>
                <p><strong>Trip Status:</strong> {{ $booking->trip_status ?? 'N/A' }}</p>
                <p><strong>Created:</strong> {{ $booking->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>

        {{-- ACTION BUTTONS – clean, no duplicates --}}
        <div class="d-flex gap-2 flex-wrap mt-3">
            <a href="{{ route('vehicle-owner.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>

            @if($booking->status === 'pending')
                <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle me-1"></i> Confirm Booking
                    </button>
                </form>
            @endif

            @if($booking->status === 'confirmed' && $booking->trip_status === 'pending')
                {{-- Start Trip form with id for GPS capture --}}
                <form id="startTripForm" action="{{ route('vehicle-owner.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i> Start Trip
                    </button>
                </form>
            @endif

            @if($booking->trip_status === 'in_progress')
                <form action="{{ route('vehicle-owner.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-flag-checkered me-1"></i> Complete Trip
                    </button>
                </form>
            @endif

            @if($booking->trip_status === 'completed')
                <span class="badge bg-secondary fs-6 p-2">Trip Completed</span>
            @endif

            <a href="{{ route('tracking.ride', $booking) }}" class="btn btn-info">
                <i class="fas fa-map-marked-alt me-1"></i> Track
            </a>
        </div>
    </div>
</div>

@push('scripts')
{{-- Optional GPS Location Capture on Start Trip --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startForm = document.getElementById('startTripForm');
        if (startForm) {
            startForm.addEventListener('submit', function(e) {
                // Prevent default submission temporarily
                e.preventDefault();

                // Check if geolocation is available
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            console.log('📍 Location captured:', lat, lng);

                            // Option 1: Append hidden fields to the form
                            const hiddenLat = document.createElement('input');
                            hiddenLat.type = 'hidden';
                            hiddenLat.name = 'start_lat';
                            hiddenLat.value = lat;
                            startForm.appendChild(hiddenLat);

                            const hiddenLng = document.createElement('input');
                            hiddenLng.type = 'hidden';
                            hiddenLng.name = 'start_lng';
                            hiddenLng.value = lng;
                            startForm.appendChild(hiddenLng);

                            // Option 2: Send via AJAX to a dedicated endpoint (optional)
                            // You can implement a route that stores the location
                            // For now, we submit the form with the hidden fields.
                            startForm.submit();
                        },
                        function(error) {
                            console.warn('⚠️ Location permission denied or error:', error);
                            // Proceed without location – form will still submit
                            startForm.submit();
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                } else {
                    console.warn('⚠️ Geolocation not supported');
                    startForm.submit();
                }
            });
        }
    });
</script>
@endpush
@endsection