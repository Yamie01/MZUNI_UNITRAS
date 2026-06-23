@extends('vehicle-owner.layouts.owner')

@section('title', 'Advertisement Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Advertisement Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Title:</strong> {{ $advertisement->title }}</p>
                <p><strong>Route:</strong> {{ $advertisement->from_location }} → {{ $advertisement->to_location }}</p>
                <p><strong>Departure:</strong> {{ \Carbon\Carbon::parse($advertisement->departure_time)->format('d M Y H:i') }}</p>
                <p><strong>Price per Seat:</strong> MWK {{ number_format($advertisement->price, 2) }}</p>
                <p><strong>Total Seats:</strong> {{ $advertisement->total_seats }}</p>
                <p><strong>Available Seats:</strong> {{ $advertisement->available_seats }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong>
                    @if($advertisement->status === 'approved')
                        <span class="badge bg-success">Approved</span>
                    @elseif($advertisement->status === 'pending')
                        <span class="badge bg-warning">Pending</span>
                    @elseif($advertisement->status === 'rejected')
                        <span class="badge bg-danger">Rejected</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst($advertisement->status) }}</span>
                    @endif
                </p>
                <p><strong>Ad Type:</strong> {{ ucfirst(str_replace('_', ' ', $advertisement->ad_type)) }}</p>
                <p><strong>Trip Status:</strong>
                    @if($advertisement->trip_status === 'scheduled')
                        <span class="badge bg-info">Scheduled</span>
                    @elseif($advertisement->trip_status === 'in_progress')
                        <span class="badge bg-success">In Transit</span>
                    @elseif($advertisement->trip_status === 'completed')
                        <span class="badge bg-secondary">Completed</span>
                    @else
                        <span class="badge bg-secondary">Unknown</span>
                    @endif
                </p>
                <p><strong>Created:</strong> {{ $advertisement->created_at->format('d M Y H:i') }}</p>
                <p><strong>Bookings:</strong> {{ $advertisement->bookings->count() }}</p>
            </div>
        </div>

        <hr>

        {{-- Action Buttons (unified) --}}
        <div class="d-flex gap-2 flex-wrap">
            {{-- Trip control buttons --}}
            @if($advertisement->trip_status === 'scheduled')
                <form action="{{ route('vehicle-owner.advertisements.start-trip', $advertisement) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-1"></i> Start Trip
                    </button>
                </form>
            @elseif($advertisement->trip_status === 'in_progress')
                <form action="{{ route('vehicle-owner.advertisements.complete-trip', $advertisement) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-flag-checkered me-1"></i> Complete Trip
                    </button>
                </form>
            @elseif($advertisement->trip_status === 'completed')
                <span class="badge bg-secondary fs-6 p-2">Trip Completed</span>
            @endif

            {{-- Edit button (only for pending ads) --}}
            @if($advertisement->status === 'pending')
                <a href="{{ route('vehicle-owner.advertisements.edit', $advertisement) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            @endif

            {{-- Back button --}}
            <a href="{{ route('vehicle-owner.advertisements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection