@extends('layouts.admin')

@section('title', 'Advertisement Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ $advertisement->title }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Description:</strong> {{ $advertisement->description }}</p>
                    <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $advertisement->ad_type)) }}</p>
                    <p><strong>From:</strong> {{ $advertisement->from_location }}</p>
                    <p><strong>To:</strong> {{ $advertisement->to_location }}</p>
                    <p><strong>Departure:</strong> {{ $advertisement->departure_time->format('d M Y H:i') }}</p>
                    <p><strong>Price:</strong> MWK {{ number_format($advertisement->price, 2) }}</p>
                    <p><strong>Seats:</strong> {{ $advertisement->available_seats }} / {{ $advertisement->total_seats }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $advertisement->status == 'approved' ? 'success' : ($advertisement->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($advertisement->status) }}
                        </span>
                    </p>
                </div>
            </div>

            @if($advertisement->bookings->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5>Bookings ({{ $advertisement->bookings->count() }})</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Seats</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($advertisement->bookings as $booking)
                            <tr>
                                <td>{{ $booking->user->name }}</td>
                                <td>{{ $booking->number_of_seats }}</td>
                                <td>MWK {{ number_format($booking->total_price, 2) }}</td>
                                <td>{{ ucfirst($booking->status) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Vehicle Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Reg:</strong> {{ $advertisement->vehicle->registration_number ?? 'N/A' }}</p>
                    <p><strong>Model:</strong> {{ $advertisement->vehicle->model ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($advertisement->vehicle->vehicle_type ?? 'N/A') }}</p>
                    <p><strong>Capacity:</strong> {{ $advertisement->vehicle->capacity ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Owner Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $advertisement->owner->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $advertisement->owner->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $advertisement->owner->phone ?? 'N/A' }}</p>
                </div>
            </div>

            @if($advertisement->status == 'pending')
            <div class="d-flex gap-2">
                <form action="{{ route('admin.advertisements.approve', $advertisement) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
            </div>
            @endif
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('admin.advertisements.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.advertisements.reject', $advertisement) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Advertisement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Reason for rejection..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection