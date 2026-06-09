@extends('layouts.app')

@section('title', 'Subscription Plans - Mzuni UNITRAS')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">🚀 Choose Your Plan</h1>
        <p class="text-muted">Subscribe and get free rides – pay once, ride for days!</p>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if($activeSubscription)
        <div class="alert alert-success mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <i class="fas fa-check-circle fa-2x me-3 float-start"></i>
                    <div>
                        <strong>Active Subscription: {{ ucfirst($activeSubscription->type) }} Pass</strong>
                        <br>
                        <small>Expires: {{ $activeSubscription->end_date->format('d M Y, H:i') }}</small>
                        <br>
                        <small>Today's rides: {{ $usageStats['today_used'] ?? 0 }} / {{ $usageStats['today_limit'] ?? 0 }}</small>
                    </div>
                </div>
                <div>
                    <span class="badge bg-success fs-6">Active</span>
                    <form action="{{ route('subscription.cancel', $activeSubscription) }}" method="POST" class="d-inline ms-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel your subscription?')">Cancel</button>

                    </form>
                </div>
            </div>
        </div>
    @endif
    @if($activeSubscription && $activeSubscription->status === 'pending')
    <div class="alert alert-warning mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-clock me-2"></i>
                Your subscription payment is pending verification.
            </div>
            <form action="{{ route('subscription.manual-verify') }}" method="POST">
                @csrf
                <input type="hidden" name="subscription_id" value="{{ $activeSubscription->id }}">
                <button type="submit" class="btn btn-sm btn-warning">
                    <i class="fas fa-check-circle me-1"></i> Verify Payment
                </button>
            </form>
        </div>
    </div>
@endif

@if(auth()->user()->user_type === 'admin' && $activeSubscription && $activeSubscription->status === 'pending')
    <div class="mt-2">
        <form action="{{ route('subscription.activate', $activeSubscription) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Force activate this subscription?')">
                Force Activate (Admin)
            </button>
        </form>
    </div>
@endif
    
    <div class="row g-4">
        <!-- Weekly Plan -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Weekly Pass</h3>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4 fw-bold text-primary">MWK 5,000</h2>
                    <p class="text-muted">per week</p>
                    <hr>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> 7 days unlimited access</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Up to 2 rides per day</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Save 30%</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 pb-4 text-center">
                    @if(!$activeSubscription || $activeSubscription->type !== 'weekly')
                        <form action="{{ route('subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="weekly">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Subscribe Weekly</button>
                        </form>
                    @else
                        <button class="btn btn-success btn-lg px-4" disabled>✓ Current Plan</button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Monthly Plan -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h3 class="mb-0">Monthly Pass</h3>
                    <span class="badge bg-warning text-dark mt-2">🔥 Best Value</span>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4 fw-bold text-primary">MWK 15,000</h2>
                    <p class="text-muted">per month</p>
                    <hr>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> 30 days unlimited access</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Up to 4 rides per day</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Save 50%</li>
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 pb-4 text-center">
                    @if(!$activeSubscription || $activeSubscription->type !== 'monthly')
                        <form action="{{ route('subscription.subscribe') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="monthly">
                            <button type="submit" class="btn btn-primary btn-lg px-4">Subscribe Monthly</button>
                        </form>
                    @else
                        <button class="btn btn-success btn-lg px-4" disabled>✓ Current Plan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    @if($pastSubscriptions->count() > 0)
        <div class="mt-5">
            <h5>📜 Subscription History</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr><th>Plan</th><th>Price</th><th>Start Date</th><th>End Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($pastSubscriptions as $sub)
                            <tr>
                                <td>{{ ucfirst($sub->type) }}</td>
                                <td>MWK {{ number_format($sub->price, 2) }}</td>
                                <td>{{ $sub->start_date->format('d M Y') }}</td>
                                <td>{{ $sub->end_date->format('d M Y') }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($sub->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection