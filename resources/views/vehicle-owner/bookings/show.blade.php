@extends('vehicle-owner.layouts.owner')

@section('title', 'Booking Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Booking #{{ $booking->id }} Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Customer:</strong> {{ $booking->user->name }}</p>
                            <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                            <p><strong>Phone:</strong> {{ $booking->user->phone }}</p>
                            <p><strong>Number of Seats:</strong> {{ $booking->number_of_seats }}</p>
                            <p><strong>Total Price:</strong> MWK {{ number_format($booking->total_price, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pickup Point:</strong> {{ $booking->pickup_point }}</p>
                            <p><strong>Dropoff Point:</strong> {{ $booking->dropoff_point }}</p>
                            <p><strong>Booking Date:</strong> {{ $booking->created_at->format('d M Y H:i') }}</p>
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
                            @if($booking->special_requests)
                                <p><strong>Special Requests:</strong> {{ $booking->special_requests }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Trip Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Advertisement:</strong> {{ $booking->advertisement->title }}</p>
                    <p><strong>Route:</strong> {{ $booking->advertisement->from_location }} → {{ $booking->advertisement->to_location }}</p>
                    <p><strong>Departure:</strong> {{ $booking->advertisement->departure_time->format('d M Y H:i') }}</p>
                    @if($booking->advertisement->arrival_time)
                        <p><strong>Arrival:</strong> {{ $booking->advertisement->arrival_time->format('d M Y H:i') }}</p>
                    @endif
                </div>
            </div>

            @if($booking->payment)
            <div class="card">
                <div class="card-header">
                    <h5>Payment Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Transaction ID:</strong> {{ $booking->payment->transaction_id }}</p>
                    <p><strong>Amount:</strong> MWK {{ number_format($booking->payment->amount, 2) }}</p>
                    <p><strong>Method:</strong> {{ ucfirst($booking->payment->payment_method) }}</p>
                    <p><strong>Status:</strong> 
                        @if($booking->payment->status == 'completed')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-warning">{{ ucfirst($booking->payment->status) }}</span>
                        @endif
                    </p>
                    <p><strong>Payment Date:</strong> {{ $booking->payment->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Update Status</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($booking->status == 'pending')
                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Confirm this booking?')">
                                    <i class="fas fa-check"></i> Confirm Booking
                                </button>
                            </form>
                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Cancel this booking?')">
                                    <i class="fas fa-times"></i> Cancel Booking
                                </button>
                            </form>
                        @elseif($booking->status == 'confirmed')
                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-primary w-100 mb-2" onclick="return confirm('Mark as completed?')">
                                    <i class="fas fa-check-double"></i> Mark Completed
                                </button>
                            </form>
                            <form action="{{ route('vehicle-owner.bookings.update', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Cancel this confirmed booking? This will free up seats.')">
                                    <i class="fas fa-ban"></i> Cancel Booking
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5>Customer Contact</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-phone me-2"></i> {{ $booking->user->phone }}</p>
                    <p><i class="fas fa-envelope me-2"></i> {{ $booking->user->email }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="{{ route('vehicle-owner.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>
</div>
@endsection