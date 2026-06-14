@extends('layouts.app')

@section('title', 'Complete Payment - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Complete Payment</h4>
                </div>
                <div class="card-body">
                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <h6>Booking Summary</h6>
                        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                        <p><strong>Ride:</strong> {{ $booking->advertisement->title ?? 'N/A' }}</p>
                        <p><strong>Route:</strong> {{ $booking->advertisement->from_location ?? 'N/A' }} → {{ $booking->advertisement->to_location ?? 'N/A' }}</p>
                        <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                        <hr>
                        <h5 class="text-primary">Total Amount: MWK {{ number_format($booking->total_price, 2) }}</h5>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How to pay:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Click "Pay Now" to proceed to PayChangu secure payment page</li>
                            <li>Select your payment method (Airtel Money, TNM Mpamba, or Card)</li>
                            <li>Enter your mobile money number or card details</li>
                            <li>You will receive a push notification to complete payment</li>
                            <li>After payment, you'll be redirected back to confirm your booking</li>
                        </ul>
                    </div>
                    
                    <!-- Pay Button -->
                    <div class="d-grid gap-2">
                       <a href="{{ route('payment.initiate', $booking) }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Pay MWK {{ number_format($booking->total_price, 2) }}
                        </a>
                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection