@extends('layouts.app')

@section('title', 'Rent a Bike')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bicycle me-2"></i> Rent {{ $bike->brand }} {{ $bike->model }}</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-bicycle fa-5x text-primary"></i>
                    </div>

                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-6">
                                <p><strong>Type:</strong> {{ ucfirst($bike->type) }}</p>
                                <p><strong>Hourly:</strong> MWK {{ number_format($bike->price_per_hour, 0) }}</p>
                            </div>
                            <div class="col-6">
                                <p><strong>Daily:</strong> MWK {{ number_format($bike->price_per_day, 0) }}</p>
                                <p><strong>Deposit:</strong> MWK {{ number_format($bike->deposit_amount, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('user.bikes.rent.process', $bike) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Duration</label>
                            <input type="number" name="duration" class="form-control" value="1" min="1" max="30" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Duration Type</label>
                            <select name="duration_type" class="form-select" required>
                                <option value="hour">Hour(s)</option>
                                <option value="day">Day(s)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pickup Location</label>
                            <input type="text" name="pickup_location" class="form-control" placeholder="Enter pickup location" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dropoff Location</label>
                            <input type="text" name="dropoff_location" class="form-control" placeholder="Enter dropoff location" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-bicycle me-2"></i> Rent Now
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="{{ route('user.bikes.index') }}" class="btn btn-link">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection