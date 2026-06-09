@extends('layouts.app')

@section('title', 'My Bookings - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">My Bookings</h2>
            <p class="text-muted">View and manage your ride bookings</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-body">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Booking ID</th>
                                <th>Route</th>
                                <th>Seats</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Booking Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}<br><small class="text-muted">{{ $booking->booking_reference }}</small></td>
                                <td>
                                    {{ $booking->advertisement->from_location ?? 'N/A' }} → 
                                    {{ $booking->advertisement->to_location ?? 'N/A' }}
                                    <br><small class="text-muted">{{ \Carbon\Carbon::parse($booking->advertisement->departure_time)->format('d M Y H:i') }}</small>
                                </td>
                                <td>{{ $booking->number_of_seats }}</td>
                                <td>MWK {{ number_format($booking->total_price, 2) }}</td>
                                <td>
                                    @if($booking->status == 'confirmed')
                                        <span class="badge bg-success">Confirmed</span>
                                    @elseif($booking->status == 'pending')
                                        <span class="badge bg-warning">Pending Payment</span>
                                    @elseif($booking->status == 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>{{ $booking->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($booking->status == 'pending')
                                            <a href="{{ route('user.bookings.payment', $booking) }}" class="btn btn-success">
                                                <i class="fas fa-credit-card"></i> Pay
                                            </a>
                                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this booking?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <td>
                                            @if($booking->booking_type === 'subscription')
                                                <span class="badge bg-success">Free (Subscription)</span>
                                            @else
                                                <span class="badge bg-primary">Paid</span>
                                            @endif
                                        </td>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                    <h5>No bookings yet</h5>
                    <p class="text-muted">You haven't made any ride bookings.</p>
                    <a href="{{ route('search') }}" class="btn btn-primary">
                        <i class="fas fa-search"></i> Find Rides
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection