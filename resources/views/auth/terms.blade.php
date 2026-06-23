@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Terms & Conditions</h4>
        </div>
        <div class="card-body">
            <h5>1. Acceptance of Terms</h5>
            <p>By using Mzuni UNITRAS, you agree to these terms and conditions.</p>

            <h5>2. User Accounts</h5>
            <p>You must provide accurate information when creating an account. You are responsible for maintaining the confidentiality of your account.</p>

            <h5>3. Vehicle Owners</h5>
            <p>Vehicle owners must ensure their vehicles are properly insured and maintained. All vehicles must be approved by the admin.</p>

            <h5>4. Payments</h5>
            <p>All payments are processed securely through PayChangu. A 20% platform fee applies to all ride bookings.</p>

            <h5>5. Cancellations</h5>
            <p>Bookings can be cancelled within 24 hours of booking. Late cancellations may incur a fee.</p>

            <h5>6. Privacy</h5>
            <p>Your personal information is protected and will not be shared without your consent.</p>

            <h5>7. Termination</h5>
            <p>We reserve the right to terminate accounts that violate these terms.</p>

            <a href="{{ route('register') }}" class="btn btn-primary mt-3">Back to Registration</a>
        </div>
    </div>
</div>
@endsection