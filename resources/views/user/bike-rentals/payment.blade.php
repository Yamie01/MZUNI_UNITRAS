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
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Payment Summary -->
                    <div class="alert alert-info">
                        <h6>Payment Summary</h6>
                        <p><strong>Rental Code:</strong> {{ $rental->rental_code }}</p>
                        <p><strong>Bike:</strong> {{ $rental->bike->brand }} {{ $rental->bike->model }}</p>
                        <p><strong>Duration:</strong> {{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</p>
                        <hr>
                        <h5 class="text-primary">Total Amount: MWK {{ number_format($rental->total_amount, 2) }}</h5>
                    </div>
                    
                    <form action="{{ route('payment.initiateRental', $rental) }}" method="POST" id="paymentForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Select Mobile Money Provider</label>
                            <select name="provider" class="form-control" required>
                                <option value="">Select Provider</option>
                                <option value="airtel">Airtel Money</option>
                                <option value="tnm">TNM Mpamba</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mobile Money Number</label>
                            <input type="tel" name="phone_number" class="form-control" 
                                   placeholder="e.g., 0990000000" required>
                            <small class="text-muted">Test number: Airtel: 0990000000, TNM: 0899817565</small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>How it works:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Click "Pay Now" to proceed to PayChangu secure payment page</li>
                                <li>Select your payment method (Airtel Money or TNM Mpamba)</li>
                                <li>Enter your mobile money number</li>
                                <li>You will receive a USSD push to complete payment</li>
                                <li>After payment, you'll be redirected back automatically</li>
                            </ul>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="payBtn">
                                <i class="fas fa-credit-card me-2"></i> Pay MWK {{ number_format($rental->total_amount, 2) }}
                            </button>
                            <a href="{{ route('user.bike-rentals.show', $rental) }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('payBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
    });
</script>
@endpush