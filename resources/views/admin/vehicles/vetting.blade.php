@extends('layouts.app')

@section('title', 'Vehicle Vetting Dashboard')

@section('content')
<div class="container">
    <h2>Vehicle Vetting Dashboard</h2>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">{{ $pendingVehicles->count() }}</h5>
                    <p class="card-text">Pending Vetting</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">{{ $manualReviewVehicles->count() }}</h5>
                    <p class="card-text">Manual Review Needed</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">{{ $rejectedVehicles->count() }}</h5>
                    <p class="card-text">Rejected</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">{{ $vettedVehicles->count() }}</h5>
                    <p class="card-text">Approved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Vehicles -->
    <div class="card mb-4">
        <div class="card-header">
            Pending Vehicles ({{ $pendingVehicles->count() }})
            <form action="{{ route('admin.vetting.bulk') }}" method="POST" class="d-inline float-end">
                @csrf
                <button class="btn btn-primary btn-sm">Vet All Pending</button>
            </form>
        </div>
        <div class="card-body">
            @if($pendingVehicles->isEmpty())
                <p>No pending vehicles.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Vehicle</th>
                            <th>Plate</th>
                            <th>Vetting Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingVehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->owner->name }}</td>
                            <td>{{ $vehicle->name }} {{ $vehicle->model }}</td>
                            <td>{{ $vehicle->license_plate }}</td>
                            <td>{{ $vehicle->vetting_score }}%</td>
                            <td>
                                <a href="{{ route('admin.vetting.show', $vehicle) }}" class="btn btn-sm btn-info">Review</a>
                                <form action="{{ route('admin.vetting.approve', $vehicle) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Approve</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Manual Review Vehicles -->
    <div class="card mb-4">
        <div class="card-header">Need Manual Review ({{ $manualReviewVehicles->count() }})</div>
        <div class="card-body">
            @if($manualReviewVehicles->isEmpty())
                <p>No vehicles need manual review.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Owner</th>
                            <th>Vehicle</th>
                            <th>Plate</th>
                            <th>Score</th>
                            <th>Checks</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manualReviewVehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->owner->name }}</td>
                            <td>{{ $vehicle->name }} {{ $vehicle->model }}</td>
                            <td>{{ $vehicle->license_plate }}</td>
                            <td>{{ $vehicle->vetting_score }}%</td>
                            <td>
                                <button class="btn btn-sm btn-secondary" data-bs-toggle="collapse" data-bs-target="#checks-{{ $vehicle->id }}">
                                    Show Checks
                                </button>
                                <div id="checks-{{ $vehicle->id }}" class="collapse mt-2">
                                    <ul class="list-unstyled small">
                                        @foreach($vehicle->vetting_checks as $check)
                                        <li>
                                            <i class="fas fa-{{ $check['passed'] ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                                            {{ $check['name'] }}: {{ $check['message'] }}
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.vetting.show', $vehicle) }}" class="btn btn-sm btn-info">Review</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection