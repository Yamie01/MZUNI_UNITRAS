@extends('layouts.vehicle-owner')

@section('title', 'Create Advertisement - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Create New Advertisement</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vehicle-owner.advertisements.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Select Vehicle *</label>
                        <select name="vehicle_id" class="form-select" required>
                            <option value="">Choose a vehicle</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">
                                    {{ $vehicle->model }} ({{ $vehicle->registration_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Advertisement Type *</label>
                        <select name="ad_type" class="form-select" required>
                            <option value="ride_share">Ride Share</option>
                            <option value="taxi">Taxi</option>
                            <option value="bus">Bus</option>
                            <option value="bike_share">Bike Share</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">From Location *</label>
                        <input type="text" name="from_location" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">To Location *</label>
                        <input type="text" name="to_location" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Departure Date & Time *</label>
                        <input type="datetime-local" name="departure_time" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Seat (MWK) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Total Seats *</label>
                        <input type="number" name="total_seats" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Available Seats *</label>
                        <input type="number" name="available_seats" class="form-control" required>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Advertisement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection