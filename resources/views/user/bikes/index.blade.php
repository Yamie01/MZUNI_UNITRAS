@extends('layouts.app')

@section('title', 'Bike Sharing - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold">Bike Sharing</h2>
            <p class="text-muted">Rent a bike for your campus commute. Affordable, eco-friendly, and convenient.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.bikes.index') }}" class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="mountain" {{ request('type') == 'mountain' ? 'selected' : '' }}>Mountain</option>
                        <option value="road" {{ request('type') == 'road' ? 'selected' : '' }}>Road</option>
                        <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        <option value="electric" {{ request('type') == 'electric' ? 'selected' : '' }}>Electric</option>
                        <option value="city" {{ request('type') == 'city' ? 'selected' : '' }}>City</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price" value="{{ request('min_price') }}">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price" value="{{ request('max_price') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('user.bike-rentals.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-history"></i> My Rentals
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bikes Grid -->
    <div class="row g-4">
        @forelse($bikes as $bike)
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="card h-100 shadow-sm">
                <!-- Simple Image without complex array handling -->
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         class="card-img-top" 
                         alt="{{ $bike->brand }} {{ $bike->model }}" 
                         style="height: 180px; object-fit: cover;">
                    <span class="badge bg-primary position-absolute top-0 end-0 m-2">{{ ucfirst($bike->type) }}</span>
                </div>
                
                <div class="card-body">
                    <h5 class="card-title">{{ $bike->brand }} {{ $bike->model }}</h5>
                    <p class="card-text text-muted small">{{ $bike->bike_code }}</p>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Hourly:</span>
                            <strong class="text-primary">MWK {{ number_format($bike->price_per_hour, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Daily:</span>
                            <strong>MWK {{ number_format($bike->price_per_day, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Deposit:</span>
                            <strong>MWK {{ number_format($bike->deposit_amount, 2) }}</strong>
                        </div>
                    </div>
                    
                    @if($bike->features)
                        @php
                            $features = is_string($bike->features) ? json_decode($bike->features, true) : $bike->features;
                        @endphp
                        @if(is_array($features) && count($features) > 0)
                        <div class="mb-3">
                            @foreach($features as $feature)
                                <span class="badge bg-light text-dark me-1">{{ $feature }}</span>
                            @endforeach
                        </div>
                        @endif
                    @endif
                </div>
                
                <div class="card-footer bg-transparent">
                    <a href="{{ route('user.bikes.show', $bike) }}" class="btn btn-primary w-100">
                        <i class="fas fa-bicycle me-1"></i> View Details
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5 bg-light rounded">
                <i class="fas fa-bicycle fa-4x text-muted mb-3"></i>
                <h5>No bikes available</h5>
                <p class="text-muted">Check back later for bike availability.</p>
            </div>
        </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $bikes->links() }}
    </div>
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endsection