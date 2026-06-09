@extends('layouts.app')

@section('title', 'Payment Status - Mzuni UNITRAS')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Payment Status</h4>
                </div>
                <div class="card-body text-center">
                    <div id="paymentPending">
                        <div class="spinner-border text-primary mb-3" style="width: 4rem; height: 4rem;" role="status">
                            <span class="visually-hidden">Waiting for payment...</span>
                        </div>
                        <h5>Waiting for Payment Confirmation</h5>
                        <p>Please check your phone. A payment request has been sent to your mobile money.<br>
                        Open the notification and enter your PIN to complete payment.</p>
                        <div class="alert alert-info">
                            <strong>Rental Code:</strong> {{ $rental->rental_code }}
                        </div>
                        <p class="text-muted small">This page will automatically update when payment is confirmed.</p>
                    </div>
                    
                    <div id="paymentSuccess" style="display: none;">
                        <i class="fas fa-check-circle text-success mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-success">Payment Successful!</h5>
                        <p>Your payment has been confirmed. You will receive an SMS confirmation shortly.</p>
                        <a href="{{ route('user.bike-rentals.show', $rental) }}" class="btn btn-primary">
                            View Your Rental
                        </a>
                    </div>
                    
                    <div id="paymentError" style="display: none;">
                        <i class="fas fa-times-circle text-danger mb-3" style="font-size: 4rem;"></i>
                        <h5 class="text-danger">Payment Pending</h5>
                        <p>We haven't received payment confirmation yet.</p>
                        <a href="{{ route('user.bike-rentals.payment', $rental) }}" class="btn btn-warning">
                            Try Again
                        </a>
                        <a href="{{ route('user.bike-rentals.show', $rental) }}" class="btn btn-secondary">
                            View Rental
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let checkCount = 0;
    const maxChecks = 30; // Check for 60 seconds (every 2 seconds)
    
    function checkPaymentStatus() {
        fetch('{{ route('user.bike-rentals.payment.check-status', $rental) }}')
            .then(response => response.json())
            .then(data => {
                if (data.is_paid) {
                    document.getElementById('paymentPending').style.display = 'none';
                    document.getElementById('paymentSuccess').style.display = 'block';
                } else if (checkCount >= maxChecks) {
                    document.getElementById('paymentPending').style.display = 'none';
                    document.getElementById('paymentError').style.display = 'block';
                } else {
                    checkCount++;
                    setTimeout(checkPaymentStatus, 2000);
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
                if (checkCount >= maxChecks) {
                    document.getElementById('paymentPending').style.display = 'none';
                    document.getElementById('paymentError').style.display = 'block';
                } else {
                    checkCount++;
                    setTimeout(checkPaymentStatus, 2000);
                }
            });
    }
    
    // Start checking payment status after 3 seconds
    setTimeout(checkPaymentStatus, 3000);
</script>
@endsection