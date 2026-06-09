@extends('layouts.admin')

@section('title', 'Add New Bike - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Add New Bike</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.bikes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Code *</label>
                        <input type="text" name="bike_code" class="form-control @error('bike_code') is-invalid @enderror" 
                               value="{{ old('bike_code') }}" required placeholder="e.g., BIKE-001">
                        @error('bike_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bike Type *</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="mountain" {{ old('type') == 'mountain' ? 'selected' : '' }}>Mountain</option>
                            <option value="road" {{ old('type') == 'road' ? 'selected' : '' }}>Road</option>
                            <option value="hybrid" {{ old('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            <option value="electric" {{ old('type') == 'electric' ? 'selected' : '' }}>Electric</option>
                            <option value="city" {{ old('type') == 'city' ? 'selected' : '' }}>City</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Brand *</label>
                        <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" 
                               value="{{ old('brand') }}" required>
                        @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Model *</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" 
                               value="{{ old('model') }}" required>
                        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" name="color" class="form-control" value="{{ old('color') }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Year</label>
                        <input type="number" name="year" class="form-control" value="{{ old('year') }}" 
                               min="2000" max="{{ date('Y') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Hour (MWK) *</label>
                        <input type="number" step="0.01" name="price_per_hour" class="form-control @error('price_per_hour') is-invalid @enderror" 
                               value="{{ old('price_per_hour', 500) }}" required>
                        @error('price_per_hour')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price per Day (MWK) *</label>
                        <input type="number" step="0.01" name="price_per_day" class="form-control @error('price_per_day') is-invalid @enderror" 
                               value="{{ old('price_per_day', 3000) }}" required>
                        @error('price_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Deposit Amount (MWK) *</label>
                        <input type="number" step="0.01" name="deposit_amount" class="form-control @error('deposit_amount') is-invalid @enderror" 
                               value="{{ old('deposit_amount', 5000) }}" required>
                        @error('deposit_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Features</label>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="GPS" id="gps">
                                    <label class="form-check-label" for="gps">GPS</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Phone Holder" id="phone">
                                    <label class="form-check-label" for="phone">Phone Holder</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Water Bottle Holder" id="bottle">
                                    <label class="form-check-label" for="bottle">Water Bottle Holder</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="features[]" value="Helmet Included" id="helmet">
                                    <label class="form-check-label" for="helmet">Helmet Included</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label">Images</label>
                        <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        <small class="text-muted">You can upload multiple images</small>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('admin.bikes.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Bike</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection