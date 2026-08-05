@extends('layouts.app')

@section('title', 'QR Code Preview')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📱 QR Code Preview</h4>
                </div>
                <div class="card-body text-center">
                    <h5>{{ $bike->brand }} {{ $bike->model }}</h5>
                    <p class="text-muted">Registration: {{ $bike->registration_number }}</p>
                    
                    <div class="my-4">
                        @if($bike->qr_code_path)
                            <img src="{{ asset('storage/' . $bike->qr_code_path) }}" alt="QR Code" class="img-fluid" style="max-width: 300px; border: 2px solid #ddd; border-radius: 10px; padding: 10px;">
                        @else
                            <p class="text-danger">No QR code generated yet.</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <p><strong>QR Code ID:</strong> <code>{{ $bike->qr_code }}</code></p>
                        <p><strong>Activation URL:</strong> <a href="{{ $bike->qr_activation_url }}" target="_blank" class="text-break">{{ $bike->qr_activation_url }}</a></p>
                    </div>

                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('admin.bikes.download-qr', $bike) }}" class="btn btn-success">
                            <i class="fas fa-download me-2"></i> Download
                        </a>
                        <a href="{{ route('admin.bikes.generate-qr', $bike) }}" class="btn btn-warning">
                            <i class="fas fa-sync me-2"></i> Regenerate
                        </a>
                        <a href="{{ route('admin.bikes.print-labels') }}" class="btn btn-info">
                            <i class="fas fa-print me-2"></i> Print Labels
                        </a>
                        <a href="{{ route('admin.bikes.show', $bike) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection