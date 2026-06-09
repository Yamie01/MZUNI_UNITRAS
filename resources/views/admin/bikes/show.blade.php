@extends('layouts.admin')

@section('title', 'Bike Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Bike Details: {{ $bike->bike_code }}</h5>
            <div>
                <a href="{{ route('admin.bikes.edit', $bike) }}" class="btn btn-warning btn-sm">Edit Bike</a>
                <a href="{{ route('admin.bikes.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Basic Information -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Basic Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Bike Code</th>
                                    <td>{{ $bike->bike_code }}</td>
                                </tr>
                                <tr>
                                    <th>Brand</th>
                                    <td>{{ $bike->brand }}</td>
                                </tr>
                                <tr>
                                    <th>Model</th>
                                    <td>{{ $bike->model }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>{{ ucfirst($bike->type) }}</td>
                                </tr>
                                <tr>
                                    <th>Color</th>
                                    <td>{{ $bike->color ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Year</th>
                                    <td>{{ $bike->year ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'available' => 'success',
                                                'rented' => 'warning',
                                                'maintenance' => 'danger',
                                                'out_of_service' => 'secondary'
                                            ];
                                            $color = $statusColors[$bike->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($bike->status) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Active</th>
                                    <td>
                                        @if($bike->is_active)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Pricing Information</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Price per Hour</th>
                                    <td>MWK {{ number_format($bike->price_per_hour, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Price per Day</th>
                                    <td>MWK {{ number_format($bike->price_per_day, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Deposit Amount</th>
                                    <td>MWK {{ number_format($bike->deposit_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Description</h6>
                        </div>
                        <div class="card-body">
                            <p>{{ $bike->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Features</h6>
                        </div>
                        <div class="card-body">
                            @php 
                                $features = $bike->features;
                                if (is_string($features)) {
                                    $features = json_decode($features, true);
                                }
                                if (!is_array($features)) {
                                    $features = [];
                                }
                            @endphp
                            @if(count($features) > 0)
                                <div class="row">
                                    @foreach($features as $feature)
                                        @if(is_string($feature))
                                            <div class="col-md-3 mb-2">
                                                <span class="badge bg-secondary">{{ $feature }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No features listed.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Images</h6>
                        </div>
                        <div class="card-body">
                            @php 
                                $images = $bike->images;
                                if (is_string($images)) {
                                    $images = json_decode($images, true);
                                }
                                if (!is_array($images)) {
                                    $images = [];
                                }
                                // Filter out empty values and invalid entries
                                $images = array_filter($images, function($img) {
                                    return is_string($img) && !empty($img);
                                });
                            @endphp
                            @if(count($images) > 0)
                                <div class="row">
                                    @foreach($images as $image)
                                        <div class="col-md-3 mb-3">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No images uploaded.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Rental History -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Rental History</h6>
                        </div>
                        <div class="card-body">
                            @if(isset($rentals) && $rentals->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Rental Code</th>
                                                <th>Renter</th>
                                                <th>Duration</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Rental Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rentals as $rental)
                                                <tr>
                                                    <td>{{ $rental->rental_code ?? 'N/A' }}</td>
                                                    <td>{{ $rental->user->name ?? 'N/A' }}</td>
                                                    <td>{{ $rental->duration }} {{ ucfirst($rental->duration_type) }}(s)</td>
                                                    <td>MWK {{ number_format($rental->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'completed' ? 'info' : 'warning') }}">
                                                            {{ ucfirst($rental->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $rental->created_at->format('d M Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No rental history for this bike.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection