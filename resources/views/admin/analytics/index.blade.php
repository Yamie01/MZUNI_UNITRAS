@extends('layouts.admin')

@section('title', 'Analytics Dashboard - Mzuni UNITRAS')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s;
        height: 100%;
        border: 1px solid #eef2f8;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .stat-number { font-size: 2.5rem; font-weight: 800; color: #00529b; }
    .stat-label { color: #6c757d; font-size: 0.9rem; }
    .stat-icon { font-size: 2rem; opacity: 0.3; }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
        height: 320px;
    }
    
    .chart-container canvas { max-height: 250px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3"><i class="fas fa-chart-line text-primary me-2"></i>Analytics Dashboard</h1>
        <div>
            <a href="{{ route('admin.analytics.export') }}" class="btn btn-outline-success">
                <i class="fas fa-file-export me-2"></i>Export Report
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total_users'] ?? 0) }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">MWK {{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['total_bookings'] ?? 0) }}</div>
                <div class="stat-label">Total Bookings</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['active_rentals'] ?? 0) }}</div>
                <div class="stat-label">Active Rentals</div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar text-primary me-2"></i>Monthly Revenue</h6>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-container">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie text-primary me-2"></i>Booking Types</h6>
                <canvas id="bookingTypeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-container" style="height: 350px;">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-primary me-2"></i>Daily Trends (Last 30 Days)</h6>
                <canvas id="dailyTrendChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container" style="height: 350px;">
                <h6 class="fw-bold mb-3"><i class="fas fa-chart-line text-primary me-2"></i>User Growth</h6>
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-coins text-primary me-2"></i>Revenue Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5>Ride Bookings</h5>
                                <h3 class="text-primary">MWK {{ number_format($revenueBreakdown['ride_bookings'] ?? 0, 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5>Subscriptions</h5>
                                <h3 class="text-success">MWK {{ number_format($revenueBreakdown['subscriptions'] ?? 0, 0) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5>Bike Rentals</h5>
                                <h3 class="text-warning">MWK {{ number_format($revenueBreakdown['bike_rentals'] ?? 0, 0) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Routes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-route text-primary me-2"></i>Popular Routes</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Trips</th>
                                    <th>Popularity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($popularRoutes as $route)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $route->pickup_point }}</td>
                                    <td>{{ $route->dropoff_point }}</td>
                                    <td>{{ $route->count }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px;">
                                            @php
                                                $maxCount = $popularRoutes->max('count') ?? 1;
                                                $percentage = ($route->count / $maxCount) * 100;
                                            @endphp
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">No routes data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($monthlyRevenue)) !!},
                datasets: [{
                    label: 'Revenue (MWK)',
                    data: {!! json_encode(array_values($monthlyRevenue)) !!},
                    backgroundColor: 'rgba(0, 82, 155, 0.6)',
                    borderColor: '#00529b',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: function(value) { return 'MWK ' + value.toLocaleString(); } }
                    }
                }
            }
        });

        // Booking Types Chart
        new Chart(document.getElementById('bookingTypeChart'), {
            type: 'doughnut',
            data: {
                labels: ['Paid Bookings', 'Subscription', 'Bike Rentals'],
                datasets: [{
                    data: [
                        {{ $bookingTypes['paid'] ?? 0 }},
                        {{ $bookingTypes['subscription'] ?? 0 }},
                        {{ $bookingTypes['bike_rental'] ?? 0 }}
                    ],
                    backgroundColor: ['#00529b', '#198754', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Daily Trends Chart
        new Chart(document.getElementById('dailyTrendChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($dailyTrends, 'date')) !!},
                datasets: [{
                    label: 'Bookings',
                    data: {!! json_encode(array_column($dailyTrends, 'bookings')) !!},
                    borderColor: '#00529b',
                    backgroundColor: 'rgba(0, 82, 155, 0.1)',
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Rentals',
                    data: {!! json_encode(array_column($dailyTrends, 'rentals')) !!},
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // User Growth Chart
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($userGrowth)) !!},
                datasets: [{
                    label: 'Total Users',
                    data: {!! json_encode(array_values($userGrowth)) !!},
                    borderColor: '#00529b',
                    backgroundColor: 'rgba(0, 82, 155, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection