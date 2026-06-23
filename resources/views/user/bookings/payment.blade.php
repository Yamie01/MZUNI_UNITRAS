@extends('layouts.app')

@section('title', 'Complete Payment - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card me-2"></i>Complete Payment</h4>
                </div>
                <div class="card-body">

                    {{-- Payment Summary --}}
                    <div class="alert alert-info">
                        <h6 class="fw-bold">Booking Summary</h6>
                        <hr>
                        <p><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                        <p><strong>Ride:</strong> {{ $booking->advertisement->title ?? 'N/A' }}</p>
                        <p><strong>Route:</strong> 
                            {{ $booking->advertisement->fromLocation->name ?? $booking->advertisement->from_location ?? 'N/A' }} 
                            → 
                            {{ $booking->advertisement->toLocation->name ?? $booking->advertisement->to_location ?? 'N/A' }}
                        </p>
                        <p><strong>Seats:</strong> {{ $booking->number_of_seats }}</p>
                        <hr>
                        <h5 class="text-primary">Total Amount: <strong>MWK {{ number_format($booking->total_price, 2) }}</strong></h5>
                    </div>

                    {{-- Payment Instructions --}}
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>How to pay:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Click <strong>"Pay with PayChangu"</strong> to proceed to the secure payment page.</li>
                            <li>Select your preferred payment method (Airtel Money, TNM Mpamba, or Card).</li>
                            <li>Enter your mobile money number or card details.</li>
                            <li>You will receive a push notification to complete the payment.</li>
                            <li>After payment, you'll be redirected back to confirm your booking.</li>
                        </ul>
                    </div>

                    {{-- 🔥 Pay Button – only ONE (POST form) --}}
                    <form action="{{ route('user.bookings.payment.initiate', $booking) }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i> Pay MWK {{ number_format($booking->total_price, 2) }}
                            </button>
                            <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection