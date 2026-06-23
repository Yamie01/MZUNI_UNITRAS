@extends('layouts.app')

@section('title', 'My Bike Rentals')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i>My Bike Rentals</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($rentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Bike</th>
                                <th>Rental Date</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                            <tr>
                                <td>
                                    <strong>{{ $rental->bike->brand ?? 'Bike' }} {{ $rental->bike->model ?? '' }}</strong>
                                </td>
                                <td>
                                    {{ $rental->created_at->format('d M Y') }}<br>
                                    <small class="text-muted">{{ $rental->created_at->format('H:i') }}</small>
                                </td>
                                <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                                <td><strong>MWK {{ number_format($rental->total_amount ?? $rental->total_price, 0) }}</strong></td>
                                <td>
                                    @if($rental->status === 'active' || $rental->status === 'rented')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($rental->status === 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($rental->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($rental->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('user.bike-rentals.show', $rental) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>

                                    @if($rental->status === 'active' || $rental->status === 'rented')
                                        <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">Track</a>
                                        <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Return</button>
                                        </form>
                                    @endif

                                    @if($rental->status === 'pending')
                                        <form action="{{ route('user.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this rental?')">Cancel</button>
                                        </form>
                                    @endif

                                    @if($rental->late_fee > 0 && !$rental->late_fee_paid && $rental->status === 'completed')
                                        <a href="{{ route('rentals.pay-late-fee', $rental) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-credit-card me-1"></i> Pay Late Fee
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $rentals->links() }}
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bicycle fa-3x text-muted mb-3"></i>
                    <p>No bike rentals yet.</p>
                    <a href="{{ route('user.bikes.index') }}" class="btn btn-primary">Rent a Bike</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection