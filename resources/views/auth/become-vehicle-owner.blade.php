@extends('layouts.app')

@section('title', 'Become a Vehicle Owner')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-car me-2"></i> Become a Vehicle Owner</h4>
                </div>
                <div class="card-body text-center">
                    <p>To offer rides, you need to register as a <strong>Vehicle Owner</strong>.</p>
                    <p>After registering, you can add your vehicle(s) and wait for admin approval.</p>
                    <form action="{{ route('become.vehicle.owner.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i> Register as Vehicle Owner
                        </button>
                    </form>
                    <div class="mt-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-link">Maybe later</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection