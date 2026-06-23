@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Details</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                            <p><strong>Route:</strong> {{ $booking->pickup_point }} → {{ $booking->dropoff_point }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->trip_date)->format('d M Y, H:i') }}</p>
                            <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                            <p><strong>Total Price:</strong> MWK {{ number_format($booking->total_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong>
                                @if($booking->status === 'pending')
                                    <span class="badge bg-warning">Pending Payment</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="badge bg-info">Confirmed</span>
                                @elseif($booking->trip_status === 'in_progress')
                                    <span class="badge bg-success">In Transit</span>
                                @elseif($booking->trip_status === 'completed')
                                    <span class="badge bg-secondary">Completed</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </p>
                            <p><strong>Trip Status:</strong> {{ $booking->trip_status ?? 'N/A' }}</p>
                            <p><strong>Booked:</strong> {{ $booking->created_at->format('d M Y, H:i') }}</p>
                            @if($booking->booking_type === 'subscription')
                                <p><span class="badge bg-success">Free (Subscription)</span></p>
                            @endif
                        </div>
                    </div>

                    @if($booking->special_requests)
                        <div class="mt-3">
                            <strong>Special Requests:</strong>
                            <p>{{ $booking->special_requests }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($booking->status === 'pending')
                            <a href="{{ route('user.bookings.payment.initiate', $booking) }}" class="btn btn-success">
                                <i class="fas fa-credit-card me-1"></i> Pay Now
                            </a>
                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this booking?')">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </button>
                            </form>
                            @if(!$booking->is_paid)
                                <form action="{{ route('payment.manual-verify') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-check-circle me-1"></i> Verify Payment
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($booking->status === 'confirmed')
                            @if($booking->trip_status === 'pending')
                                <form action="{{ route('user.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play me-1"></i> Start Trip
                                    </button>
                                </form>
                            @elseif($booking->trip_status === 'in_progress')
                                <form action="{{ route('user.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-flag-checkered me-1"></i> Complete Trip
                                    </button>
                                </form>
                            @elseif($booking->trip_status === 'completed')
                                <span class="badge bg-secondary">Trip Completed</span>
                            @endif
                        @endif

                        <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection