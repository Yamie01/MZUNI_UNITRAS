@extends('layouts.vehicle-owner')

@section('title', 'Earnings History')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">Earnings History</h4>
            <p class="text-muted">All payments are automatically sent to your registered mobile number.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Earned</h6>
                    <h2 class="display-6 fw-bold">MWK {{ number_format($totalEarnings, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Paid to Mobile</h6>
                    <h2 class="display-6 fw-bold">MWK {{ number_format($totalPayouts, 2) }}</h2>
                    <small>Sent to {{ Auth::user()->phone }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Pending Payouts</h6>
                    <h2 class="display-6 fw-bold">MWK {{ number_format($pendingPayouts, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">Payout History</h6>
        </div>
        <div class="card-body">
            @if($payouts->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Booking</th>
                                <th>Amount</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payouts as $payout)
                            <tr>
                                <td>#{{ $payout->booking_id }}</td>
                                <td>MWK {{ number_format($payout->amount, 2) }}</td>
                                <td>{{ $payout->recipient_phone }}</td>
                                <td>
                                    <span class="badge bg-{{ $payout->status === 'completed' ? 'success' : ($payout->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td>{{ $payout->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-wallet fa-3x text-muted mb-3"></i>
                    <p>No payouts yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection