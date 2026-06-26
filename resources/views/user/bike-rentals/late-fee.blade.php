@extends('layouts.app')

@section('title', 'Pay Late Fee')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Pay Late Fee</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <h6>You have an unpaid late fee for:</h6>
                        <ul class="mb-0">
                            @foreach($unpaidRentals as $rental)
                                <li>
                                    {{ $rental->bike->brand }} {{ $rental->bike->model }}
                                    - Late Fee: MWK {{ number_format($rental->late_fee, 2) }}
                                    <br><small class="text-muted">Returned: {{ $rental->actual_return_time->format('d M Y H:i') }}</small>
                                </li>
                            @endforeach
                        </ul>
                        <hr>
                        <h5>Total Due: MWK {{ number_format($totalLateFee, 2) }}</h5>
                    </div>
                    
                    <form action="{{ route('rentals.pay-late-fee-bulk') }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-credit-card me-2"></i> Pay All Late Fees (MWK {{ number_format($totalLateFee, 2) }})
                            </button>
                            <a href="{{ route('user.bike-rentals.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection