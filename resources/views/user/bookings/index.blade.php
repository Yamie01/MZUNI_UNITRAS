@extends('layouts.app')

@section('title', 'My Ride Bookings')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i> My Ride Bookings</h4>
                </div>
                <div class="card-body">
                    {{-- Session Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show">
                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Route</th>
                                    <th>Date</th>
                                    <th>Seats</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                @php
                                    $isPassenger = $booking->user_id === auth()->id();
                                    $isVehicleOwner = $booking->advertisement->owner_id === auth()->id();
                                    $tripStatus = $booking->trip_status ?? 'pending';
                                @endphp
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $booking->pickup_point ?? $booking->advertisement->from_location ?? 'N/A' }}
                                            <i class="fas fa-arrow-right text-muted mx-1"></i>
                                            {{ $booking->dropoff_point ?? $booking->advertisement->to_location ?? 'N/A' }}
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($booking->advertisement->departure_time ?? $booking->trip_date)->format('d M Y, H:i') }}
                                        </small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}</td>
                                    <td>{{ $booking->number_of_seats }}</td>
                                    <td><strong>MWK {{ number_format($booking->total_price, 0) }}</strong></td>
                                    <td>
                                        @if($booking->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending Payment</span>
                                        @elseif($booking->status === 'confirmed' && $tripStatus === 'pending')
                                            <span class="badge bg-info">Confirmed</span>
                                        @elseif($tripStatus === 'in_progress')
                                            <span class="badge bg-success">In Transit 🚗</span>
                                        @elseif($tripStatus === 'completed' || $booking->status === 'completed')
                                            <span class="badge bg-secondary">Completed ✅</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- View Button --}}
                                        <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-sm btn-info mb-1">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Pending Booking Actions --}}
                                        @if($booking->status === 'pending')
                                            <a href="{{ route('user.bookings.payment.initiate', $booking) }}" class="btn btn-sm btn-success mb-1">
                                                <i class="fas fa-credit-card"></i>
                                            </a>
                                            @if(!$booking->is_paid)
                                                <form action="{{ route('payment.manual-verify') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" class="btn btn-sm btn-warning mb-1" onclick="return confirm('Manually verify payment for this booking?')">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Cancel this booking?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Confirmed Booking Trip Actions --}}
                                        @if($booking->status === 'confirmed')
                                            @if($tripStatus === 'pending')
                                                @if($isPassenger || $isVehicleOwner)
                                                    <form action="{{ route('user.bookings.start-trip', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Start this trip?')" title="Start Trip">
                                                            <i class="fas fa-play"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif

                                            @if($tripStatus === 'in_progress')
                                                @if($isPassenger || $isVehicleOwner)
                                                    <form action="{{ route('user.bookings.complete-trip', $booking) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary mb-1" onclick="return confirm('Complete this trip?')" title="Complete Trip">
                                                            <i class="fas fa-flag-checkered"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-calendar-times fa-2x text-muted mb-2 d-block"></i>
                                        <p class="mb-0">No ride bookings yet.</p>
                                        <a href="{{ route('search') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-search me-1"></i> Book a Ride Now
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($bookings->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection