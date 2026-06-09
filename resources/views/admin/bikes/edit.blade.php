@extends('layouts.admin')

@section('title', 'Edit Bike - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Edit Bike: {{ $bike->bike_code }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bikes.update', $bike) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Bike Code -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Code *</label>
                        <input type="text" name="bike_code" class="form-control @error('bike_code') is-invalid @enderror" 
                               value="{{ old('bike_code', $bike->bike_code) }}" required>
                        @error('bike_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Bike Type -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Type *</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="mountain" {{ old('type', $bike->type) == 'mountain' ? 'selected' : '' }}>Mountain</option>
                            <option value="road" {{ old('type', $bike->type) == 'road' ? 'selected' : '' }}>Road</option>
                            <option value="hybrid" {{ old('type', $bike->type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="electric" {{ old('type', $bike->type) == 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="city" {{ old('type', $bike->type) == 'city' ? 'selected' : '' }}>City</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Brand -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand *</label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" 
                               value="{{ old('brand', $bike->brand) }}" required>
                        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Model -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                               value="{{ old('model', $bike->model) }}" required>
                        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Color -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="{{ old('color', $bike->color) }}">
                    </div>
                    
                    <!-- Year -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ old('year', $bike->year) }}" 
                               min="2000" max="{{ date('Y') }}">
                    </div>
                    
                    <!-- Price per Hour -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Hour (MWK) *</label>
                        <input type="number" step="0.01" name="price_per_hour" class="form-control @error('price_per_hour') is-invalid @enderror" 
                               value="{{ old('price_per_hour', $bike->price_per_hour) }}" required>
                        @error('price_per_hour')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Price per Day -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Day (MWK) *</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control @error('price_per_day') is-invalid @enderror" 
                               value="{{ old('price_per_day', $bike->price_per_day) }}" required>
                        @error('price_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Deposit Amount -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Deposit Amount (MWK) *</label>
                        <input type="number" step="0.01" name="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror" 
                               value="{{ old('deposit_amount', $bike->deposit_amount) }}" required>
                        @error('deposit_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <!-- Status - Admin can change to available, rented, etc. -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Status *</label>
                        <select name="status" class="form-select" required>
                            <option value="available" {{ old('status', $bike->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="rented" {{ old('status', $bike->status) == 'rented' ? 'selected' : '' }}>Rented</option>
                            <option value="maintenance" {{ old('status', $bike->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="out_of_service" {{ old('status', $bike->status) == 'out_of_service' ? 'selected' : '' }}>Out of Service</option>
                        </select>
                    </div>
                    
                    <!-- Active Status -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Active</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $bike->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_active', $bike->is_active) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    
                    <!-- Description -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $bike->description) }}</textarea>
                    </div>
                    
                    <!-- Features (Checkboxes - Fixed Array to String Error) -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Features</label>
                        @php 
                            $features = is_array($bike->features) ? $bike->features : [];
                            if (!is_array($features)) {
                                $features = json_decode($bike->features, true) ?: [];
                            }
                        @endphp
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="GPS" id="gps" {{ in_array('GPS', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gps">GPS</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Phone Holder" id="phone" {{ in_array('Phone Holder', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="phone">Phone Holder</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Water Bottle Holder" id="bottle" {{ in_array('Water Bottle Holder', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="bottle">Water Bottle Holder</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Helmet Included" id="helmet" {{ in_array('Helmet Included', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="helmet">Helmet Included</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Lock" id="lock" {{ in_array('Lock', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="lock">Lock</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Lights" id="lights" {{ in_array('Lights', $features) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="lights">Lights</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Images -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Current Images</label>
                        @php 
                            $images = is_array($bike->images) ? $bike->images : [];
                            if (!is_array($images)) {
                                $images = json_decode($bike->images, true) ?: [];
                            }
                        @endphp
                        @if(count($images) > 0)
                            <div class="row mb-3">
                                @foreach($images as $image)
                                    @if(is_string($image))
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded" style="height: 100px; object-fit: cover;">
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No images uploaded</p>
                        @endif
                        
                        <label class="form-label mt-2">Upload New Images</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">Leave empty to keep current images. You can select multiple images.</small>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('admin.bikes.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Bike</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection