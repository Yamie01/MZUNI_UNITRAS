@extends('layouts.app')

@section('title', 'Rent Bike')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Rent {{ $bike->brand }} {{ $bike->model }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.bikes.rent.process', $bike) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dropoff Location</label>
                            <input type="text" name="dropoff_location" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration</label>
                                <input type="number" name="duration" class="form-control" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Duration Type</label>
                                <select name="duration_type" class="form-select" required>
                                    <option value="hour">Hour(s)</option>
                                    <option value="day">Day(s)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price Details</label>
                            <div class="alert alert-info">
                                Hourly: MWK {{ number_format($bike->price_per_hour) }}<br>
                                Daily: MWK {{ number_format($bike->price_per_day) }}
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Proceed to Payment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection