@extends('layouts.app')

@section('title', 'Search Rides - Mzuni UNITRAS')

@push('styles')
<style>
    .filter-sidebar {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        height: fit-content;
    }
    
    .vehicle-card {
        transition: transform 0.3s;
    }
    
    .vehicle-card:hover {
        transform: translateY(-5px);
    }
    
    .price-badge {
        font-size: 1.2rem;
        padding: 5px 15px;
    }
    
    .route-info {
        border-left: 3px solid #007bff;
        padding-left: 15px;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="filter-sidebar">
                <h5 class="mb-4"><i class="fas fa-filter me-2"></i>Filter Results</h5>
                
                <form method="GET" action="{{ route('search') }}">
                    <!-- Vehicle Type Filter -->
                    <div class="mb-4">
                        <h6>Vehicle Type</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="types[]" value="ride_share" 
                                   id="ride_share" {{ in_array('ride_share', request('types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="ride_share">
                                <i class="fas fa-car me-1"></i> Ride Share
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="types[]" value="taxi" 
                                   id="taxi" {{ in_array('taxi', request('types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="taxi">
                                <i class="fas fa-taxi me-1"></i> Taxi
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="types[]" value="bus" 
                                   id="bus" {{ in_array('bus', request('types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bus">
                                <i class="fas fa-bus me-1"></i> Bus
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="types[]" value="bike_share" 
                                   id="bike_share" {{ in_array('bike_share', request('types', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bike_share">
                                <i class="fas fa-bicycle me-1"></i> Bike Share
                            </label>
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <h6>Price Range (MWK)</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="min_price" 
                                       placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" class="form-control form-control-sm" name="max_price" 
                                       placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Seats Available -->
                    <div class="mb-4">
                        <h6>Seats Available</h6>
                        <select class="form-select form-select-sm" name="seats">
                            <option value="">Any</option>
                            <option value="1" {{ request('seats') == '1' ? 'selected' : '' }}>1 Seat</option>
                            <option value="2" {{ request('seats') == '2' ? 'selected' : '' }}>2+ Seats</option>
                            <option value="4" {{ request('seats') == '4' ? 'selected' : '' }}>4+ Seats</option>
                            <option value="10" {{ request('seats') == '10' ? 'selected' : '' }}>10+ Seats</option>
                        </select>
                    </div>
                    
                    <!-- Departure Time -->
                    <div class="mb-4">
                        <h6>Departure Time</h6>
                        <select class="form-select form-select-sm" name="departure_time">
                            <option value="">Anytime</option>
                            <option value="morning" {{ request('departure_time') == 'morning' ? 'selected' : '' }}>Morning (6AM-12PM)</option>
                            <option value="afternoon" {{ request('departure_time') == 'afternoon' ? 'selected' : '' }}>Afternoon (12PM-6PM)</option>
                            <option value="evening" {{ request('departure_time') == 'evening' ? 'selected' : '' }}>Evening (6PM-10PM)</option>
                            <option value="night" {{ request('departure_time') == 'night' ? 'selected' : '' }}>Night (10PM-6AM)</option>
                        </select>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="mb-4">
                        <h6>Sort By</h6>
                        <select class="form-select form-select-sm" name="sort">
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="departure_asc" {{ request('sort') == 'departure_asc' ? 'selected' : '' }}>Departure: Earliest</option>
                            <option value="departure_desc" {{ request('sort') == 'departure_desc' ? 'selected' : '' }}>Departure: Latest</option>
                            <option value="seats_asc" {{ request('sort') == 'seats_asc' ? 'selected' : '' }}>Seats: Fewest</option>
                            <option value="seats_desc" {{ request('sort') == 'seats_desc' ? 'selected' : '' }}>Seats: Most</option>
                        </select>
                    </div>
                    
                    <!-- Hidden Fields -->
                    @if(request('from'))
                        <input type="hidden" name="from" value="{{ request('from') }}">
                    @endif
                    @if(request('to'))
                        <input type="hidden" name="to" value="{{ request('to') }}">
                    @endif
                    @if(request('date'))
                        <input type="hidden" name="date" value="{{ request('date') }}">
                    @endif
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('search') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Search Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4>Available Rides</h4>
                    <p class="text-muted mb-0">
                        @if(request('from') || request('to'))
                            {{ request('from', 'Anywhere') }} to {{ request('to', 'Anywhere') }}
                            @if(request('date'))
                                on {{ \Carbon\Carbon::parse(request('date'))->format('M d, Y') }}
                            @endif
                        @else
                            All available vehicles
                        @endif
                    </p>
                </div>
                <div>
                    <span class="badge bg-primary">{{ $advertisements->total() }} results found</span>
                </div>
            </div>
            
            <!-- Search Results -->
            @if($advertisements->count() > 0)
                <div class="row">
                    @foreach($advertisements as $advertisement)
                    <div class="col-md-6 col-lg-4 mb-4">
                        @include('partials.vehicle-card', ['advertisement' => $advertisement])
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $advertisements->withQueryString()->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-car fa-4x text-muted"></i>
                    </div>
                    <h4>No vehicles found</h4>
                    <p class="text-muted mb-4">Try adjusting your search criteria or browse all vehicles</p>
                    <a href="{{ route('search') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Browse All Vehicles
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection