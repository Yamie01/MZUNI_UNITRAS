@extends('layouts.app')

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
        </div>
    </div>
</div>

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
@endsection