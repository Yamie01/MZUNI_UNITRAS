@extends('layouts.app')

@section('title', $bike->brand . ' ' . $bike->model . ' - Bike Details')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-6">
            @if($bike->images && is_array($bike->images) && count($bike->images) > 0)
                <div id="bikeCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($bike->images as $key => $image)
                            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 rounded" style="height: 400px; object-fit: cover;" alt="{{ $bike->brand }}">
                            </div>
                        @endforeach
                    </div>
                    @if(count($bike->images) > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#bikeCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#bikeCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="bg-secondary text-white text-center rounded py-5" style="height: 400px;">
                    <i class="fas fa-bicycle fa-5x mt-5"></i>
                    <p class="mt-3">No image available</p>
                </div>
            @endif
        </div>
        
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="mb-0">{{ $bike->brand }} {{ $bike->model }}</h2>
                            <p class="text-muted">{{ $bike->bike_code }}</p>
                        </div>
                        <span class="badge bg-success fs-6">{{ ucfirst($bike->type) }} Bike</span>
                    </div>
                    
                    <hr>
                    
                    <div class="row mb-4">
                        <div class="col-6">
                            <h4 class="text-primary">MWK {{ number_format($bike->price_per_hour, 2) }}</h4>
                            <small class="text-muted">per hour</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary">MWK {{ number_format($bike->price_per_day, 2) }}</h4>
                            <small class="text-muted">per day</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>Security Deposit:</strong> MWK {{ number_format($bike->deposit_amount, 2) }}
                        <small class="d-block">Refundable upon return with no damages</small>
                    </div>
                    
                    @if($bike->features && is_array($bike->features))
                        <div class="mb-4">
                            <h6>Features:</h6>
                            <div>
                                @foreach($bike->features as $feature)
                                    <span class="badge bg-light text-dark me-1 p-2">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($bike->description)
                        <div class="mb-4">
                            <h6>Description:</h6>
                            <p>{{ $bike->description }}</p>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        @auth
                            @if($bike->status == 'available')
                                <a href="{{ route('user.bikes.rent', $bike) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-bicycle me-2"></i> Rent This Bike
                                </a>
                            @else
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-ban me-2"></i> Not Available
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                Login to Rent
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection