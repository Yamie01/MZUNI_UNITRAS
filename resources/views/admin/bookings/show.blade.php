@extends('layouts.admin')

@section('title', 'Booking Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Booking Details #{{ $booking->id }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p><strong>Name:</strong> {{ $booking->user->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $booking->user->email ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $booking->user->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Ride Information</h6>
                            <p><strong>Title:</strong> {{ $booking->advertisement->title ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $booking->advertisement->ad_type ?? 'N/A')) }}</p>
                            <p><strong>Departure:</strong> {{ \Carbon\Carbon::parse($booking->advertisement->departure_time)->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Trip Details</h6>
                            <p><strong>From:</strong> {{ $booking->advertisement->from_location ?? 'N/A' }}</p>
                            <p><strong>To:</strong> {{ $booking->advertisement->to_location ?? 'N/A' }}</p>
                            <p><strong>Pickup Point:</strong> {{ $booking->pickup_point }}</p>
                            <p><strong>Dropoff Point:</strong> {{ $booking->dropoff_point }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Details</h6>
                            <p><strong>Seats Booked:</strong> {{ $booking->number_of_seats }}</p>
                            <p><strong>Price per Seat:</strong> MWK {{ number_format($booking->advertisement->price ?? 0, 2) }}</p>
                            <p><strong>Total Price:</strong> MWK {{ number_format($booking->total_price, 2) }}</p>
                            <p><strong>Status:</strong> 
                                @if($booking->status == 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @elseif($booking->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($booking->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @elseif($booking->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Trip Status</h6>
                            <p>
                                @if($booking->trip_status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($booking->trip_status == 'in_progress')
                                    <span class="badge bg-warning">In Progress</span>
                                @elseif($booking->trip_status == 'scheduled')
                                    <span class="badge bg-info">Scheduled</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($booking->trip_status) }}</span>
                                @endif
                            </p>
                            @if($booking->trip_started_at)
                                <p><strong>Started at:</strong> {{ $booking->trip_started_at->format('d M Y H:i') }}</p>
                            @endif
                            @if($booking->trip_completed_at)
                                <p><strong>Completed at:</strong> {{ $booking->trip_completed_at->format('d M Y H:i') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6>Revenue Details</h6>
                            <p><strong>System Commission (15%):</strong> MWK {{ number_format($booking->system_commission ?? 0, 2) }}</p>
                            <p><strong>Owner Earnings (85%):</strong> MWK {{ number_format($booking->owner_earnings ?? 0, 2) }}</p>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                    <div class="alert alert-info mt-3">
                        <strong>Special Requests:</strong> {{ $booking->special_requests }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($booking->status == 'pending')
                            <a href="#" class="btn btn-success">Confirm Booking</a>
                            <a href="#" class="btn btn-danger">Cancel Booking</a>
                        @endif
                        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Delete this booking?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">Delete Booking</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Vehicle Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Vehicle:</strong> {{ $booking->vehicle->model ?? 'N/A' }}</p>
                    <p><strong>Registration:</strong> {{ $booking->vehicle->registration_number ?? 'N/A' }}</p>
                    <p><strong>Owner:</strong> {{ $booking->advertisement->owner->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>
</div>
@endsection