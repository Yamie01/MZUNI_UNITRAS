@extends('layouts.vehicle-owner')

@section('title', 'Booking Details - Mzuni UNITRAS')

@push('styles')
<style>
    .booking-details .info-row {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .booking-details .info-row:last-child {
        border-bottom: none;
    }
    .booking-details .label {
        font-weight: 600;
        color: #475569;
        width: 140px;
        display: inline-block;
    }
    .booking-details .value {
        color: #1e293b;
    }
    .action-buttons .btn {
        min-width: 120px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-calendar-check text-primary me-2"></i>
                            Booking Details
                        </h5>
                        <span class="badge bg-secondary">{{ $booking->booking_reference }}</span>
                    </div>
                </div>
                
                <div class="card-body booking-details">
                    <!-- Status Alert -->
                    @if($booking->status === 'pending')
                        <div class="alert alert-warning border-0">
                            <i class="fas fa-clock me-2"></i>
                            This booking is pending confirmation.
                        </div>
                    @elseif($booking->status === 'confirmed' && $booking->trip_status === 'pending')
                        <div class="alert alert-info border-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Booking is confirmed. Ready to start the trip.
                        </div>
                    @elseif($booking->trip_status === 'in_progress')
                        <div class="alert alert-success border-0">
                            <i class="fas fa-route me-2"></i>
                            Trip is currently in progress.
                        </div>
                    @elseif($booking->trip_status === 'completed')
                        <div class="alert alert-secondary border-0">
                            <i class="fas fa-flag-checkered me-2"></i>
                            Trip has been completed.
                        </div>
                    @endif

                    <div class="row mt-3">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Booking Information
                            </h6>
                            <div class="info-row">
                                <span class="label">Booking Reference:</span>
                                <span class="value fw-bold">{{ $booking->booking_reference }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Customer:</span>
                                <span class="value">{{ $booking->user->name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Email:</span>
                                <span class="value">{{ $booking->user->email ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Phone:</span>
                                <span class="value">{{ $booking->user->phone ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Route:</span>
                                <span class="value">
                                    <i class="fas fa-map-pin text-danger me-1"></i>
                                    {{ $booking->pickup_point }}
                                    <i class="fas fa-arrow-right text-muted mx-2"></i>
                                    <i class="fas fa-map-marker-alt text-success me-1"></i>
                                    {{ $booking->dropoff_point }}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">Seats:</span>
                                <span class="value">{{ $booking->number_of_seats }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Total Price:</span>
                                <span class="value fw-bold text-primary">MWK {{ number_format($booking->total_price, 2) }}</span>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-cog me-2"></i>Status & Details
                            </h6>
                            <div class="info-row">
                                <span class="label">Status:</span>
                                <span class="value">
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-info">Confirmed</span>
                                    @elseif($booking->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">Trip Status:</span>
                                <span class="value">
                                    @if($booking->trip_status === 'pending')
                                        <span class="badge bg-secondary">Pending</span>
                                    @elseif($booking->trip_status === 'in_progress')
                                        <span class="badge bg-success">In Progress</span>
                                    @elseif($booking->trip_status === 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($booking->trip_status ?? 'N/A') }}</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">Payment:</span>
                                <span class="value">
                                    @if($booking->is_paid)
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Unpaid</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">Booking Type:</span>
                                <span class="value">
                                    @if($booking->booking_type === 'subscription')
                                        <span class="badge bg-info">Subscription</span>
                                    @else
                                        <span class="badge bg-secondary">Paid</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="label">Created:</span>
                                <span class="value">{{ $booking->created_at->format('d M Y H:i') }}</span>
                            </div>
                            @if($booking->trip_started_at)
                            <div class="info-row">
                                <span class="label">Trip Started:</span>
                                <span class="value">{{ \Carbon\Carbon::parse($booking->trip_started_at)->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                            @if($booking->trip_completed_at)
                            <div class="info-row">
                                <span class="label">Trip Completed:</span>
                                <span class="value">{{ \Carbon\Carbon::parse($booking->trip_completed_at)->format('d M Y H:i') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes Section -->
                    @if($booking->special_requests || $booking->driver_notes || $booking->admin_notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-bold"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                                    @if($booking->special_requests)
                                        <p><strong>Special Requests:</strong> {{ $booking->special_requests }}</p>
                                    @endif
                                    @if($booking->driver_notes)
                                        <p><strong>Driver Notes:</strong> {{ $booking->driver_notes }}</p>
                                    @endif
                                    @if($booking->admin_notes)
                                        <p><strong>Admin Notes:</strong> {{ $booking->admin_notes }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2 action-buttons">
                                <a href="{{ route('vehicle-owner.bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>

                                <!-- Status Update Dropdown -->
                                @if($booking->status === 'pending' || $booking->status === 'confirmed')
                                    <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <div class="input-group">
                                            <select name="status" class="form-select" style="width: auto;">
                                                @if($booking->status === 'pending')
                                                    <option value="confirmed">Confirm</option>
                                                @endif
                                                @if($booking->status === 'confirmed' || $booking->status === 'pending')
                                                    <option value="completed">Complete</option>
                                                @endif
                                                <option value="cancelled">Cancel</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Update
                                            </button>
                                        </div>
                                    </form>
                                @endif

                                <!-- Confirm Button (Pending only) -->
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

                                <!-- Start Trip -->
                                @if($booking->status === 'confirmed' && $booking->trip_status === 'pending')
                                    <form id="startTripForm" action="{{ route('vehicle-owner.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Start this trip?')">
                                            <i class="fas fa-play me-1"></i> Start Trip
                                        </button>
                                    </form>
                                @endif

                                <!-- Complete Trip -->
                                @if($booking->trip_status === 'in_progress')
                                    <form action="{{ route('vehicle-owner.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" onclick="return confirm('Complete this trip?')">
                                            <i class="fas fa-flag-checkered me-1"></i> Complete Trip
                                        </button>
                                    </form>
                                @endif

                                <!-- Trip Status Badges -->
                                @if($booking->trip_status === 'completed')
                                    <span class="badge bg-secondary fs-6 p-2 d-flex align-items-center">
                                        <i class="fas fa-check-circle me-1"></i> Trip Completed
                                    </span>
                                @endif

                                <!-- Track Button -->
                                <a href="{{ route('tracking.ride', $booking) }}" class="btn btn-info">
                                    <i class="fas fa-map-marked-alt me-1"></i> Track
                                </a>

                                <!-- Print Button -->
                                <button onclick="window.print()" class="btn btn-outline-secondary">
                                    <i class="fas fa-print me-1"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- GPS Location Capture on Start Trip --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startForm = document.getElementById('startTripForm');
        if (startForm) {
            startForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            console.log('📍 Location captured:', lat, lng);

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

                            startForm.submit();
                        },
                        function(error) {
                            console.warn('⚠️ Location permission denied:', error);
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