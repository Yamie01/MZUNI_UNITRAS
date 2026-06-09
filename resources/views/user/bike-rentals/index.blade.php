@extends('layouts.app')

@section('title', 'My Bike Rentals')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">My Bike Rentals</h5>
        </div>
        <div class="card-body">
            @if($rentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Rental Code</th>
                                <th>Bike</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                            <tr>
                                <td>{{ $rental->rental_code }}</td>
                                <td>{{ $rental->bike->brand }} {{ $rental->bike->model }} ({{ ucfirst($rental->bike->type) }})</td>
                                <td>{{ $rental->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                                <td>MWK {{ number_format($rental->total_amount, 2) }}</td>
                                <td>
                                    @if($rental->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($rental->status === 'completed')
                                        <span class="badge bg-info">Completed</span>
                                    @elseif($rental->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                 </td>
                                <td>
                                    @if($rental->status === 'active')
                                        <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-map-marked-alt"></i> Track
                                        </a>
                                        <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Return this bike?')">
                                                Return
                                            </button>
                                        </form>
                                    @endif
                                    @if($rental->status === 'pending' && !$rental->is_paid)
                                        <a href="{{ route('user.bike-rentals.payment', $rental) }}" class="btn btn-sm btn-primary">Pay</a>
                                        <form action="{{ route('user.bike-rentals.cancel', $rental) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this rental?')">Cancel</button>
                                        </form>
                                    @endif
                                 </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $rentals->links() }}
                </div>
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
<script>
    function confirmCancel(rentalId) {
        Swal.fire({
            title: 'Cancel Rental?',
            text: 'Are you sure you want to cancel this bike rental? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!',
            cancelButtonText: 'No, keep it'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancel-form-' + rentalId).submit();
            }
        });
    }
</script>
@endsection