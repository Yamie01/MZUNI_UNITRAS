@extends('layouts.app')

@section('title', 'My Earnings - Mzuni UNITRAS')

@push('styles')
<style>
    .dashboard-wrapper { display: flex; min-height: calc(100vh - 70px); background: #f8fafc; }
    .dashboard-sidebar { width: 280px; background: white; border-right: 1px solid #e2edf2; padding: 1.5rem 0; position: sticky; top: 70px; height: calc(100vh - 70px); }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; }
    .sidebar-nav a { display: flex; align-items: center; padding: 0.75rem 1.5rem; color: #4a6272; text-decoration: none; gap: 12px; }
    .sidebar-nav a:hover, .sidebar-nav a.active { background: #ebf8ff; color: var(--primary); border-right: 3px solid var(--primary); }
    .dashboard-main { flex: 1; padding: 1.5rem; }
    .stat-summary { background: linear-gradient(135deg, var(--primary), #003f75); border-radius: 20px; padding: 1.5rem; color: white; }
    .transaction-item { background: white; border-radius: 16px; padding: 1rem; margin-bottom: 1rem; border-left: 4px solid var(--primary); }
    @media (max-width: 768px) { .dashboard-wrapper { flex-direction: column; } .dashboard-sidebar { width: 100%; height: auto; position: relative; } }
</style>
@endpush

@section('content')
<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <div class="dashboard-sidebar">
        <div class="text-center mb-4">
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 70px; height: 70px;">
                <i class="fas fa-user-tie fa-2x"></i>
            </div>
            <h5 class="mt-2 mb-0">{{ Auth::user()->name }}</h5>
            <small class="text-muted">Vehicle Owner</small>
        </div>
        <hr>
        <ul class="sidebar-nav">
            <li><a href="{{ route('vehicle-owner.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ route('vehicle-owner.vehicles.index') }}"><i class="fas fa-car"></i> My Vehicles</a></li>
            <li><a href="{{ route('vehicle-owner.advertisements.index') }}"><i class="fas fa-ad"></i> My Ads</a></li>
            <li><a href="{{ route('vehicle-owner.bookings.index') }}"><i class="fas fa-calendar-check"></i> Bookings</a></li>
            <li><a href="{{ route('vehicle-owner.earnings') }}" class="active"><i class="fas fa-coins"></i> Earnings</a></li>
            <li><a href="{{ route('profile.edit') }}"><i class="fas fa-user-circle"></i> Profile</a></li>
            <li><hr></li>
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>

    <!-- Main Content -->
    <div class="dashboard-main">
        <div class="stat-summary mb-4">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="fw-bold mb-2">Total Earnings</h4>
                    <h2 class="display-5 fw-bold">MWK {{ number_format($totalEarnings, 2) }}</h2>
                    <p class="mb-0">From {{ $completedTrips }} completed trips</p>
                </div>
                <div class="col-md-6">
                    <h4 class="fw-bold mb-2">Pending Payout</h4>
                    <h2 class="display-5 fw-bold">MWK {{ number_format($pendingPayout, 2) }}</h2>
                    <form action="{{ route('vehicle-owner.withdraw') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-light mt-2" {{ $pendingPayout < 5000 ? 'disabled' : '' }}>
                            Request Withdrawal (min MWK 5,000)
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-bold">Transaction History</div>
            <div class="card-body">
                @if($transactions->count())
                    @foreach($transactions as $tx)
                        <div class="transaction-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ ucfirst($tx->transaction_type) }}</strong>
                                <div class="small text-muted">
                                    Reference: {{ $tx->reference }}<br>
                                    Paid: {{ $tx->paid_at ? $tx->paid_at->format('d M Y, H:i') : 'Pending' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-success">+ MWK {{ number_format($tx->owner_earnings, 2) }}</div>
                                <span class="badge bg-{{ $tx->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <p class="text-muted text-center py-4">No transactions yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection