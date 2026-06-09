@extends('layouts.app')

@section('title', 'Buses - Mzuni UNITRAS')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-bus me-2"></i>Available Buses</h2>
                <a href="{{ route('search') }}" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Search All Vehicles
                </a>
            </div>
        </div>
    </div>
    
    @if($buses && $buses->count() > 0)
        <div class="row">
            @foreach($buses as $bus)
            <!-- Bus cards will go here -->
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-bus fa-4x text-muted"></i>
            </div>
            <h4>No buses available at the moment</h4>
            <p class="text-muted">Check back later for available bus services</p>
        </div>
    @endif
</div>
@endsection