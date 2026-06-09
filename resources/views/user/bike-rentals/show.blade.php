@extends('layouts.app')

@section('title', 'Bike Rental Details - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Bike Rental Details</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Rental Information</h6>
                            <p><strong>Rental Code:</strong> {{ $rental->rental_code }}</p>
                            <p><strong>Status:</strong> 
                                @if($rental->status == 'pending')
                                    <span class="badge bg-warning">Pending Payment</span>
                                @elseif($rental->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($rental->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @elseif($rental->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </p>
                            <p><strong>Start Time:</strong> {{ $rental->start_time->format('d M Y H:i') }}</p>
                            <p><strong>Duration:</strong> {{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</p>
                            @if($rental->end_time)
                                <p><strong>Expected Return:</strong> {{ \Carbon\Carbon::parse($rental->end_time)->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Bike Information</h6>
                            <p><strong>Bike:</strong> {{ $rental->bike->brand }} {{ $rental->bike->model }}</p>
                            <p><strong>Type:</strong> {{ ucfirst($rental->bike->type) }}</p>
                            <p><strong>Pickup Location:</strong> {{ $rental->pickup_location }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p><strong>Rate:</strong> MWK {{ number_format($rental->rate_per_unit, 2) }} /{{ $rental->duration_type == 'hourly' ? 'hour' : 'day' }}</p>
                            <p><strong>Subtotal:</strong> MWK {{ number_format($rental->subtotal, 2) }}</p>
                            <p><strong>Deposit:</strong> MWK {{ number_format($rental->deposit_paid, 2) }}</p>
                            <p><strong>Total:</strong> MWK {{ number_format($rental->total_amount, 2) }}</p>
                            <p><strong>Paid:</strong> {{ $rental->is_paid ? 'Yes' : 'No' }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($rental->notes)
                                <h6>Notes:</h6>
                                <p>{{ $rental->notes }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('user.bike-rentals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Rentals
                        </a>
                        
                        @if($rental->status == 'pending')
                            <a href="{{ route('user.bike-rentals.payment', $rental) }}" class="btn btn-success">
                                <i class="fas fa-credit-card me-1"></i> Complete Payment
                            </a>
                            <form action="{{ route('user.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this rental?')">
                                    <i class="fas fa-times me-1"></i> Cancel Rental
                                </button>
                            </form>
                        @endif
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($rental->status === 'active')
    <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" onclick="return confirm('Mark this bike as returned?')">
            <i class="fas fa-undo-alt me-1"></i> Return Bike
        </button>
    </form>
@endif
@if($rental->status === 'active')
    <div class="alert alert-info" id="timer-display">
        <i class="fas fa-stopwatch me-2"></i>
        <span id="timer-countdown">Calculating remaining time...</span>
    </div>
    
    <script>
        function updateRentalTimer() {
            const startTime = {{ $rental->created_at->timestamp }};
            const duration = {{ $rental->duration }};
            const durationType = '{{ $rental->duration_type }}';
            let durationHours = durationType === 'hour' ? duration : duration * 24;
            const endTime = startTime + (durationHours * 3600);
            const now = Math.floor(Date.now() / 1000);
            
            if (now > endTime) {
                const overtime = now - endTime;
                const overtimeHours = Math.ceil(overtime / 3600);
                document.getElementById('timer-countdown').innerHTML = `<span class="text-danger">OVERTIME: +${overtimeHours} hour(s)</span>`;
            } else {
                const remaining = endTime - now;
                const hours = Math.floor(remaining / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                document.getElementById('timer-countdown').innerHTML = `Time remaining: ${hours}h ${minutes}m`;
            }
        }
        
        updateRentalTimer();
        setInterval(updateRentalTimer, 60000);
    </script>
@endif
@endsection