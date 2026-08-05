@extends('layouts.app')

<<<<<<< HEAD
@section('title', 'Complete Payment - Mzuni UNITRAS')
=======
@section('title', 'Payment - Ride Booking')
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
<<<<<<< HEAD
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Complete Payment</h4>
                </div>
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <h6>Booking Summary</h6>
                        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                        <p><strong>Ride:</strong> {{ $booking->advertisement->title ?? 'N/A' }}</p>
                        <p><strong>Route:</strong> {{ $booking->pickup_point }} → {{ $booking->dropoff_point }}</p>
                        <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                        <hr>
                        <h5 class="text-primary">Total Amount: MWK {{ number_format($booking->total_price, 2) }}</h5>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How to pay:</strong>
                        <ul class="mb-0 mt-2">
=======
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">💰 Complete Payment</h4>
                </div>
                <div class="card-body">
                    <!-- Booking Details -->
                    <div class="mb-4">
                        <h5>Ride: {{ $booking->advertisement->title ?? 'Ride' }}</h5>
                        <p class="text-muted mb-1">
                            <strong>Route:</strong> 
                            {{ $booking->advertisement->from_location ?? 'N/A' }} → 
                            {{ $booking->advertisement->to_location ?? 'N/A' }}
                        </p>
                        <p class="text-muted mb-1">
                            <strong>Seats:</strong> {{ $booking->seats }}
                        </p>
                        <p class="text-muted mb-1">
                            <strong>Total Amount:</strong> 
                            <span class="text-success fw-bold">MWK {{ number_format($booking->total_amount, 2) }}</span>
                        </p>
                    </div>

                    <hr>

                    <!-- Payment Instructions -->
                    <div class="mb-4">
                        <h6>How to pay:</h6>
                        <ol class="text-muted small">
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                            <li>Click "Pay with PayChangu" to proceed to the secure payment page.</li>
                            <li>Select your preferred payment method (Airtel Money, TNM Mpamba, or Card).</li>
                            <li>Enter your mobile money number or card details.</li>
                            <li>You will receive a push notification to complete the payment.</li>
                            <li>After payment, you'll be redirected back to confirm your booking.</li>
<<<<<<< HEAD
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('user.bookings.payment.initiate', $booking) }}" class="btn btn-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Pay with PayChangu
                        </a>
                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
=======
                        </ol>
                    </div>

                    <!-- Payment Buttons -->
                    <div class="d-flex gap-2">
                        <form action="{{ route('user.bookings.payment.initiate', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Pay with PayChangu
                            </button>
                        </form>
                        
                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-outline-danger btn-lg">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                    </div>

                    <!-- Security Badge -->
                    <div class="mt-4 text-center">
                        <span class="badge bg-success">
                            <i class="fas fa-shield-alt me-1"></i> Secured by PayChangu
                        </span>
                    </div>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
                </div>
            </div>
        </div>
    </div>
</div>
@endsection