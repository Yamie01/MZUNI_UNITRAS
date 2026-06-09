@extends('layouts.app')

@section('title', 'All Vehicles - Mzuni UNITRAS')

@section('content')
<div class="container py-4">

    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-car me-2"></i>All Vehicles</h2>
            <p class="text-muted">Browse all available transportation options</p>
        </div>
    </div>

    <div class="row">

        <!-- Buses -->
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-bus fa-3x text-primary"></i>
                    </div>

                    <h4>Buses</h4>
                    <p>University and inter-city bus services</p>

                    <a href="{{ route('buses.index') }}" class="btn btn-outline-primary">
                        View Buses <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Cars -->
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-car fa-3x text-success"></i>
                    </div>

                    <h4>Cars & Taxis</h4>
                    <p>Ride sharing and taxi services</p>

                    <a href="{{ route('cars.index') }}" class="btn btn-outline-success">
                        View Cars <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Bikes -->
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-bicycle fa-3x text-info"></i>
                    </div>

                    <h4>Bikes</h4>
                    <p>Bike sharing and rental services</p>

                    <a href="{{ route('bikes.index') }}" class="btn btn-outline-info">
                        View Bikes <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection