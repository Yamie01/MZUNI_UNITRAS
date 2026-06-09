@extends('layouts.app')

@section('title', 'Booking Details - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Booking Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Booking Information</h6>
                            <p><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                            <p><strong>Reference:</strong> {{ $booking->booking_reference }}</p>
                            <p><strong>Status:</strong> 
                                @if($booking->status == 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($booking->status == 'pending')
                                    <span class="badge bg-warning">Pending Payment</span>
                                @elseif($booking->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </p>
                            <p><strong>Booking Date:</strong> {{ $booking->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                            <p><strong>Price per Seat:</strong> MWK {{ number_format($booking->price_per_seat, 2) }}</p>
                            <p><strong>Total Amount:</strong> MWK {{ number_format($booking->total_price, 2) }}</p>
                            @if($booking->payment)
                                <p><strong>Transaction ID:</strong> {{ $booking->payment->transaction_id }}</p>
                                <p><strong>Payment Date:</strong> {{ $booking->payment->payment_date->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Trip Details</h6>
                            <p><strong>Ride:</strong> {{ $booking->advertisement->title ?? 'N/A' }}</p>
                            <p><strong>From:</strong> {{ $booking->advertisement->from_location ?? 'N/A' }}</p>
                            <p><strong>To:</strong> {{ $booking->advertisement->to_location ?? 'N/A' }}</p>
                            <p><strong>Departure:</strong> {{ \Carbon\Carbon::parse($booking->advertisement->departure_time)->format('d M Y H:i') }}</p>
                            <p><strong>Pickup Point:</strong> {{ $booking->pickup_point }}</p>
                            <p><strong>Drop-off Point:</strong> {{ $booking->dropoff_point }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Vehicle Information</h6>
                            <p><strong>Vehicle:</strong> {{ $booking->vehicle->model ?? 'N/A' }}</p>
                            <p><strong>Registration:</strong> {{ $booking->vehicle->registration_number ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($booking->vehicle->vehicle_type ?? 'N/A') }}</p>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                    <div class="alert alert-info mt-3">
                        <strong>Special Requests:</strong> {{ $booking->special_requests }}
                    </div>
                    @endif
                    
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('user.bookings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                        
                        @if($booking->status == 'pending')
                            <a href="{{ route('user.bookings.payment', $booking) }}" class="btn btn-success">
                                <i class="fas fa-credit-card"></i> Complete Payment
                            </a>
                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this booking?')">
                                    Cancel Booking
                                </button>
                            </form>
                        @endif
                        @if($booking->booking_type === 'subscription')
                            <div class="alert alert-success">
                                <i class="fas fa-ticket-alt me-2"></i>
                                <strong>Free Booking!</strong> This ride was covered by your subscription pass.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection