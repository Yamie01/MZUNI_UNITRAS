@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Complete Payment</h2>
    <p>Booking #{{ $booking->id }}</p>
    <p>Total amount: MWK {{ number_format($booking->total_price, 2) }}</p>
    <form method="POST" action="{{ route('payment.process', $booking) }}">
        @csrf
        <button type="submit" class="btn btn-success">Simulate Payment</button>
    </form>
</div>
@endsection