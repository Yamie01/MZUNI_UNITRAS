@extends('layouts.app')

@section('title', 'My Ride Bookings')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">My Ride Bookings</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Route</th>
                                <th>Date</th>
                                <th>Seats</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    {{ $booking->advertisement->from_location ?? 'N/A' }} →
                                    {{ $booking->advertisement->to_location ?? 'N/A' }}
                                    <br><small class="text-muted">
                                        {{ \Carbon\Carbon::parse($booking->advertisement->departure_time)->format('d M Y H:i') }}
                                    </small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($booking->trip_date)->format('d M Y') }}</td>
                                <td>{{ $booking->number_of_seats }}</td>
                                <td>MWK {{ number_format($booking->total_price, 0) }}</td>
                                <td>
                                    @if($booking->status === 'pending')
                                        <span class="badge bg-warning">Pending Payment</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="badge bg-success">Confirmed</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- View button always -->
                                    <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-sm btn-info mb-1">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if($booking->status === 'pending')
                                        <!-- Pay Now button -->
                                        <a href="{{ route('payment.initiate', $booking) }}" class="btn btn-sm btn-success mb-1">
                                            <i class="fas fa-credit-card"></i> Pay Now
                                        </a>

                                        <!-- Cancel button -->
                                        <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Cancel this booking?')">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        </form>

                                        <!-- Verify Payment button (manual fallback) -->
                                        <form action="{{ route('payment.manual-verify-booking') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                            <button type="submit" class="btn btn-sm btn-warning mb-1">
                                                <i class="fas fa-check-circle"></i> Verify
                                            </button>
                                        </form>
                                    @endif

                                    @if($booking->booking_type === 'subscription')
                                        <span class="badge bg-success">Free (Subscription)</span>
                                    @endif
                                </td>
                             </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $bookings->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-car fa-3x text-muted mb-3"></i>
                    <p>No ride bookings yet.</p>
                    <a href="{{ route('search') }}" class="btn btn-primary">Find a Ride</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection