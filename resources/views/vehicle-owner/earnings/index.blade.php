@extends('layouts.vehicle-owner')

@section('title', 'My Earnings - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>My Earnings</h2>
    </div>

    <!-- Earnings Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Earnings</h6>
                            <h2 class="mb-0">MWK {{ number_format($summary['total_earnings'], 2) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Available Balance</h6>
                            <h2 class="mb-0">MWK {{ number_format($summary['available_balance'], 2) }}</h2>
                        </div>
                        <i class="fas fa-wallet fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Withdrawn</h6>
                            <h2 class="mb-0">MWK {{ number_format($summary['withdrawn_amount'], 2) }}</h2>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white border-0 shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Commission Rate</h6>
                            <h2 class="mb-0">{{ $commissionRate }}%</h2>
                        </div>
                        <i class="fas fa-percent fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Revenue Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <h6 class="text-muted">System Commission ({{ $commissionRate }}%)</h6>
                            <h3 class="text-danger">MWK {{ number_format($totalCommission, 2) }}</h3>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Your Earnings ({{ 100 - $commissionRate }}%)</h6>
                            <h3 class="text-success">MWK {{ number_format($summary['total_earnings'], 2) }}</h3>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-danger" style="width: {{ $commissionRate }}%"></div>
                        <div class="progress-bar bg-success" style="width: {{ 100 - $commissionRate }}%"></div>
                    </div>
                    <div class="mt-3 text-center">
                        <small class="text-muted">For every MWK 100, you earn MWK {{ 100 - $commissionRate }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Withdrawal Information:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Minimum withdrawal: MWK {{ number_format($summary['minimum_withdrawal'], 2) }}</li>
                            <li>Processing time: 2-3 business days</li>
                            <li>No withdrawal fees</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Total Amount</th>
                            <th>Commission</th>
                            <th>Your Earnings</th>
                        </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                        <tr>
                            <td>{{ $booking->trip_completed_at ? $booking->trip_completed_at->format('d M Y') : $booking->created_at->format('d M Y') }}</td>
                            <td>{{ $booking->user->name }}<br><small class="text-muted">{{ $booking->user->phone }}</small></td>
                            <td>{{ $booking->advertisement->from_location }} → {{ $booking->advertisement->to_location }}</small></td>
                            <td>MWK {{ number_format($booking->total_price, 2) }}</td>
                            <td>MWK {{ number_format($booking->system_commission, 2) }}(<small>{{ $commissionRate }}%</small>)</small></td>
                            <td class="text-success fw-bold">MWK {{ number_format($booking->owner_earnings, 2) }}<br><small>({{ 100 - $commissionRate }}%)</small></td>
                        </table>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                                <p class="mb-0">No completed trips yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Withdrawal Request Form -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-hand-holding-usd me-2"></i>Request Withdrawal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vehicle-owner.withdraw') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Amount (MWK)</label>
                            <input type="number" name="amount" class="form-control" 
                                   min="{{ $summary['minimum_withdrawal'] }}" 
                                   max="{{ $summary['available_balance'] }}" 
                                   required>
                            <small class="text-muted">Minimum: MWK {{ number_format($summary['minimum_withdrawal'], 2) }} | Available: MWK {{ number_format($summary['available_balance'], 2) }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="mobile_money">Mobile Money (Airtel/TNM)</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Details</label>
                            <input type="text" name="account_details" class="form-control" 
                                   placeholder="Mobile number or bank account number" required>
                            <small class="text-muted">Enter your mobile money number or bank account details</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" 
                                onclick="return confirm('Confirm withdrawal request?')">
                            <i class="fas fa-paper-plane me-2"></i>Submit Withdrawal Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Withdrawal History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->created_at->format('d M Y') }}</td>
                                    <td>MWK {{ number_format($withdrawal->amount, 2) }}</td>
                                    <td>{{ str_replace('_', ' ', ucfirst($withdrawal->payment_method)) }}</small></td>
                                    <td>
                                        @if($withdrawal->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($withdrawal->status == 'processing')
                                            <span class="badge bg-info">Processing</span>
                                        @elseif($withdrawal->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($withdrawal->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <p class="mb-0">No withdrawal requests yet.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    .bg-gradient-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    .opacity-50 {
        opacity: 0.5;
    }
</style>
@endsection