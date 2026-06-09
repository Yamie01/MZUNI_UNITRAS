@extends('layouts.admin')

@section('title', 'Rental Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Rental Details: {{ $rental->rental_code }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> {{ $rental->user->name }}</p>
                            <p><strong>Email:</strong> {{ $rental->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $rental->user->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Bike:</strong> {{ $rental->bike->bike_code }} ({{ $rental->bike->brand }} {{ $rental->bike->model }})</p>
                            <p><strong>Status:</strong> 
                                @if($rental->status == 'active')
                                    <span class="badge bg-warning">Active</span>
                                @elseif($rental->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Rental Information</h6>
                            <p><strong>Start Time:</strong> {{ $rental->start_time->format('d M Y H:i') }}</p>
                            <p><strong>Duration:</strong> {{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</p>
                            <p><strong>Pickup Location:</strong> {{ $rental->pickup_location }}</p>
                            @if($rental->end_time)
                                <p><strong>Scheduled End:</strong> {{ $rental->end_time->format('d M Y H:i') }}</p>
                            @endif
                            @if($rental->actual_return_time)
                                <p><strong>Actual Return:</strong> {{ $rental->actual_return_time->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Information</h6>
                            <p><strong>Rate:</strong> MWK {{ number_format($rental->rate_per_unit, 2) }} /{{ $rental->duration_type == 'hourly' ? 'hour' : 'day' }}</p>
                            <p><strong>Subtotal:</strong> MWK {{ number_format($rental->subtotal, 2) }}</p>
                            <p><strong>Deposit:</strong> MWK {{ number_format($rental->deposit_paid, 2) }}</p>
                            <p><strong>Total Amount:</strong> MWK {{ number_format($rental->total_amount, 2) }}</p>
                            @if($rental->damage_charge > 0)
                                <p><strong>Damage Charge:</strong> MWK {{ number_format($rental->damage_charge, 2) }}</p>
                                <p><strong>Refund Amount:</strong> MWK {{ number_format($rental->refund_amount, 2) }}</p>
                            @endif
                            <p><strong>Paid:</strong> {{ $rental->is_paid ? 'Yes' : 'No' }}</p>
                            @if($rental->payment_date)
                                <p><strong>Payment Date:</strong> {{ $rental->payment_date->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($rental->notes)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Notes:</strong>
                            <p>{{ $rental->notes }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($rental->damage_report)
                    <div class="row mt-3">
                        <div class="col-12">
                            <strong>Damage Report:</strong>
                            <p class="text-danger">{{ $rental->damage_report }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            @if($rental->status == 'active')
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Complete Rental</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.bike-rentals.complete', $rental) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Drop-off Location</label>
                            <input type="text" name="dropoff_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Damage Report (if any)</label>
                            <textarea name="damage_report" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Damage Charge (MWK)</label>
                            <input type="number" step="0.01" name="damage_charge" class="form-control" value="0">
                        </div>
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Mark this rental as completed?')">
                            Complete Rental
                        </button>
                    </form>
                </div>
            </div>
            @endif
            
            <div class="card">
                <div class="card-header">
                    <h5>Customer Contact</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-phone me-2"></i> {{ $rental->user->phone }}</p>
                    <p><i class="fas fa-envelope me-2"></i> {{ $rental->user->email }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.bike-rentals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Rentals
        </a>
    </div>
</div>
@endsection