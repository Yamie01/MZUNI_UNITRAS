@extends('layouts.app')

<<<<<<< HEAD
@section('title', 'Bike Rental - ' . $rental->rental_code)

@push('styles')
<style>
    .timer-display {
        font-size: 3.5rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        color: #00529b;
    }
    .timer-active {
        color: #28a745;
        animation: pulse 1s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }
    .cost-display {
        font-size: 1.8rem;
        font-weight: 700;
        color: #0D6EFD;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-bicycle me-2"></i>
                        Rental #{{ $rental->rental_code }}
                    </h5>
                    <span class="badge {{ $rental->status_badge }}">{{ $rental->status_label }}</span>
                </div>
                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <!-- Verification Details -->
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-6">
                                <strong><i class="fas fa-id-card me-1"></i> ID:</strong>
                                {{ $rental->registration_number ?? 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong><i class="fas fa-phone me-1"></i> Phone:</strong>
                                {{ $rental->phone_number ?? 'N/A' }}
                            </div>
                        </div>
                    </div>

                    <!-- Rental Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Bike:</strong> {{ $rental->bike->brand }} {{ $rental->bike->model }}</p>
                            <p><strong>Status:</strong> <span class="badge {{ $rental->status_badge }}">{{ $rental->status_label }}</span></p>
                            <p><strong>Start Time:</strong> {{ $rental->start_time->format('d M Y, H:i') }}</p>
                            @if($rental->end_time)
                                <p><strong>End Time:</strong> {{ $rental->end_time->format('d M Y, H:i') }}</p>
                                <p><strong>Total Minutes:</strong> {{ $rental->total_minutes }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Pickup:</strong> {{ $rental->pickup_location }}</p>
                            <p><strong>Dropoff:</strong> {{ $rental->dropoff_location }}</p>
                            <p><strong>Rate:</strong> MWK {{ number_format($rental->rate_per_minute, 2) }}/min</p>
                            @if($rental->is_paid)
                                <p><strong>Payment:</strong> <span class="badge bg-success">Paid</span></p>
                                <p><strong>Paid On:</strong> {{ $rental->payment_date->format('d M Y, H:i') }}</p>
                            @else
                                <p><strong>Payment:</strong> <span class="badge bg-warning text-dark">Pending</span></p>
                            @endif
                        </div>
                    </div>

                    <!-- Active Rental - Live Timer -->
                    @if($rental->status === 'active')
                        <div class="card bg-light mb-4 border border-success">
                            <div class="card-body text-center">
                                <h6 class="text-muted">⏱️ Live Timer - MWK 2 per minute</h6>
                                <div class="timer-display timer-active" id="timerDisplay">
                                    {{ $rental->elapsed_time ?? '00:00:00' }}
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <small>Time Elapsed</small>
                                        <div><strong id="elapsedDisplay">{{ $rental->elapsed_time ?? '0m' }}</strong></div>
                                    </div>
                                    <div class="col-6">
                                        <small>Current Cost</small>
                                        <div class="cost-display" id="costDisplay">
                                            MWK {{ number_format($rental->current_cost ?? 0, 2) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <h5>Total Due: <span id="totalDueDisplay" class="text-primary">MWK {{ number_format($rental->current_cost ?? 0, 2) }}</span></h5>
                                </div>
                                <div class="mt-3">
                                    <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Return this bike?')">
                                            <i class="fas fa-undo-alt me-2"></i> Return Bike
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Completed Rental - Payment Required -->
                    @if($rental->status === 'completed' && !$rental->is_paid)
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-clock me-2"></i>Rental Summary</h6>
                            <div class="row">
                                <div class="col-6">Total Minutes:</div>
                                <div class="col-6"><strong>{{ $rental->total_minutes }}</strong></div>
                                <div class="col-6">Rate:</div>
                                <div class="col-6"><strong>MWK {{ number_format($rental->rate_per_minute, 2) }}/min</strong></div>
                                <div class="col-6">Total Amount:</div>
                                <div class="col-6"><strong class="text-primary">MWK {{ number_format($rental->total_amount, 2) }}</strong></div>
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <a href="{{ route('user.bike-rentals.initiate-payment', $rental) }}" class="btn btn-success">
                                    <i class="fas fa-credit-card me-2"></i> Pay via PayChangu
                                </a>
                                <form action="{{ route('user.bike-rentals.mark-paid', $rental) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" onclick="return confirm('Mark this rental as paid?')">
                                        <i class="fas fa-check me-2"></i> Mark as Paid
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($rental->status === 'completed' && $rental->is_paid)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Payment completed successfully! <strong>MWK {{ number_format($rental->total_amount, 2) }}</strong>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('user.bike-rentals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        @if($rental->status === 'active')
                            <a href="{{ route('tracking.bike', $rental) }}" class="btn btn-info">
                                <i class="fas fa-map-marked-alt me-1"></i> Track
                            </a>
                        @endif
                    </div>
                </div>
            </div>
=======
@section('title', 'Bike Rental Details')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Rental Header -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">🚲 Rental #{{ $rental->rental_code }}</h4>
                        <span class="badge bg-{{ $rental->status === 'active' ? 'success' : ($rental->status === 'completed' ? 'info' : 'secondary') }}">
                            {{ ucfirst($rental->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bike Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-bicycle me-2"></i>Bike Details</h6>
                            <p class="mb-1">
                                <strong>Brand:</strong> {{ $rental->bike->brand ?? 'N/A' }} 
                                {{ $rental->bike->model ?? '' }}
                            </p>
                            <p class="mb-1">
                                <strong>Type:</strong> {{ ucfirst($rental->bike->type ?? 'N/A') }}
                            </p>
                            <p class="mb-0">
                                <strong>Location:</strong> {{ $rental->pickup_location ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock me-2"></i>Rental Details</h6>
                            <p class="mb-1">
                                <strong>Started:</strong> 
                                {{ $rental->start_time ? $rental->start_time->format('d M Y, H:i') : 'N/A' }}
                            </p>
                            <p class="mb-1">
                                <strong>Ended:</strong> 
                                {{ $rental->end_time ? $rental->end_time->format('d M Y, H:i') : 'Active' }}
                            </p>
                            <p class="mb-0">
                                <strong>Duration:</strong> 
                                {{ $rental->total_minutes ?? 0 }} minutes
                            </p>
                        </div>
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6><i class="fas fa-calculator me-2"></i>Cost Breakdown</h6>
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <td><strong>Rate per minute</strong></td>
                                    <td>MWK {{ number_format($rental->rate_per_minute ?? 2, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Minutes</strong></td>
                                    <td>{{ $rental->total_minutes ?? 0 }} minutes</td>
                                </tr>
                                <tr>
                                    <td><strong>Subtotal</strong></td>
                                    <td>MWK {{ number_format($rental->subtotal ?? 0, 2) }}</td>
                                </tr>
                                @if(($rental->late_fee ?? 0) > 0)
                                <tr class="text-danger">
                                    <td><strong>Late Fee</strong></td>
                                    <td>MWK {{ number_format($rental->late_fee, 2) }}</td>
                                </tr>
                                @endif
                                <tr class="fw-bold">
                                    <td><strong>Total Amount</strong></td>
                                    <td>MWK {{ number_format($rental->total_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Status & Actions -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6><i class="fas fa-info-circle me-2"></i>Status</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-{{ $rental->status === 'active' ? 'success' : ($rental->status === 'completed' ? 'info' : 'secondary') }} p-2">
                                    <i class="fas fa-circle me-1"></i>
                                    {{ ucfirst($rental->status) }}
                                </span>
                                <span class="badge bg-{{ $rental->is_paid ? 'success' : 'warning' }} p-2">
                                    <i class="fas fa-{{ $rental->is_paid ? 'check-circle' : 'clock' }} me-1"></i>
                                    {{ $rental->is_paid ? 'Paid' : 'Pending Payment' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex flex-wrap gap-2">
                                @if($rental->status === 'active')
                                    <form action="{{ route('user.bike-rentals.return', $rental) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-undo me-1"></i> Return Bike
                                        </button>
                                    </form>
                                @endif

                                @if($rental->status === 'completed' && !$rental->is_paid)
                                    <a href="{{ route('user.bike-rentals.initiate-payment', $rental) }}" class="btn btn-success">
                                        <i class="fas fa-credit-card me-1"></i> Pay Now (MWK {{ number_format($rental->total_amount ?? 0, 2) }})
                                    </a>
                                    <form action="{{ route('user.bike-rentals.mark-paid', $rental) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success" onclick="return confirm('Mark this rental as paid manually?')">
                                            <i class="fas fa-check me-1"></i> Mark as Paid (Manual)
                                        </button>
                                    </form>
                                @endif

                                @if($rental->status === 'active' || $rental->status === 'completed')
                                    <a href="{{ route('user.bike-rentals.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back to Rentals
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Timer (Active Rental) -->
            @if($rental->status === 'active')
            <div class="card mt-4 shadow-sm" id="liveTimer">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Live Timer</h5>
                </div>
                <div class="card-body text-center">
                    <h2 class="display-4" id="timerDisplay">00:00:00</h2>
                    <p class="text-muted">Current Cost: <strong id="costDisplay">MWK 0.00</strong></p>
                    <p class="small text-muted">Rate: MWK {{ number_format($rental->rate_per_minute ?? 2, 2) }} per minute</p>
                </div>
            </div>
            @endif

            <!-- Payment History -->
            @if($rental->payments && $rental->payments->count() > 0)
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i> Payment History</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rental->payments as $payment)
                            <tr>
                                <td>{{ $payment->paid_at ? $payment->paid_at->format('d M Y, H:i') : 'N/A' }}</td>
                                <td>MWK {{ number_format($payment->amount ?? 0, 2) }}</td>
                                <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                                <td>
                                    <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($payment->status ?? 'N/A') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        </div>
    </div>
</div>

<<<<<<< HEAD
@if($rental->status === 'active')
<script>
    (function() {
        // Get the start time from the server
        const startTime = new Date('{{ $rental->start_time->toISOString() }}').getTime();
        const ratePerMinute = {{ $rental->rate_per_minute ?? 2.00 }};
        
        // Get DOM elements
        const timerDisplay = document.getElementById('timerDisplay');
        const elapsedDisplay = document.getElementById('elapsedDisplay');
        const costDisplay = document.getElementById('costDisplay');
        const totalDueDisplay = document.getElementById('totalDueDisplay');
        
        // Format time as HH:MM:SS
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            
            const h = String(hours).padStart(2, '0');
            const m = String(minutes).padStart(2, '0');
            const s = String(secs).padStart(2, '0');
            
            return h + ':' + m + ':' + s;
        }
        
        // Format time as "Xh Ym" or "Ym Zs"
        function formatTimeShort(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);
            
            if (hours > 0) {
                return hours + 'h ' + minutes + 'm ' + secs + 's';
            } else {
                return minutes + 'm ' + secs + 's';
            }
        }
        
        // Update the timer and cost
        function updateTimer() {
            const now = Date.now();
            const elapsedSeconds = Math.floor((now - startTime) / 1000);
            
            // Don't go negative
            const displaySeconds = Math.max(0, elapsedSeconds);
            
            // Calculate cost (MWK 2 per minute)
            const elapsedMinutes = Math.ceil(displaySeconds / 60);
            const cost = elapsedMinutes * ratePerMinute;
            
            // Update displays
            if (timerDisplay) {
                timerDisplay.textContent = formatTime(displaySeconds);
                timerDisplay.style.color = '#28a745';
                timerDisplay.style.fontWeight = '700';
            }
            
            if (elapsedDisplay) {
                elapsedDisplay.textContent = formatTimeShort(displaySeconds);
            }
            
            if (costDisplay) {
                costDisplay.textContent = 'MWK ' + cost.toFixed(2);
            }
            
            if (totalDueDisplay) {
                totalDueDisplay.textContent = 'MWK ' + cost.toFixed(2);
            }
        }
        
        // Update immediately
        updateTimer();
        
        // Update every second
        const interval = setInterval(updateTimer, 1000);
        console.log('⏱️ Timer started, updating every second');
        
        // Clean up interval when leaving the page
        window.addEventListener('beforeunload', function() {
            clearInterval(interval);
        });
    })();
</script>
@endif
=======
@push('scripts')
@if($rental->status === 'active')
<script>
    // Live Timer Script
    (function() {
        const startTime = new Date('{{ $rental->start_time ? $rental->start_time->toIso8601String() : now()->toIso8601String() }}');
        const ratePerMinute = {{ $rental->rate_per_minute ?? 2 }};
        
        function updateTimer() {
            const now = new Date();
            const diffSeconds = Math.floor((now - startTime) / 1000);
            const minutes = Math.floor(diffSeconds / 60);
            const seconds = diffSeconds % 60;
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            
            // Update display
            document.getElementById('timerDisplay').textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(remainingMinutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
            
            // Update cost
            const cost = (minutes * ratePerMinute);
            document.getElementById('costDisplay').textContent = 
                'MWK ' + cost.toFixed(2);
        }
        
        // Update every second
        updateTimer();
        setInterval(updateTimer, 1000);
    })();
</script>
@endif
@endpush
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
@endsection