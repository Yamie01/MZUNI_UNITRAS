@extends('layouts.admin')

@section('title', 'Manage Vehicles - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="table-container">
        <div class="table-header">
            <h4 class="table-title">All Vehicles</h4>
        </div>

        <table class="table data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Registration</th>
                    <th>Model</th>
                    <th>Owner</th>
                    <th>Type</th>
                    <th>Capacity</th>
                    <th>Approved</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->id }}</td>
                    <td>{{ $vehicle->registration_number }}</td>
                    <td>{{ $vehicle->model }}</td>
                    <td>{{ $vehicle->owner->name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($vehicle->vehicle_type) }}</td>
                    <td>{{ $vehicle->capacity }}</td>
                    <td>
                        @if($vehicle->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                            @if($vehicle->rejection_reason)
                                <i class="fas fa-info-circle text-danger" data-bs-toggle="tooltip" title="{{ $vehicle->rejection_reason }}"></i>
                            @endif
                        @endif
                    </td>

                    @if(!$vehicle->is_approved)
    <form action="{{ route('admin.vehicles.approve', $vehicle) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm btn-success">Approve</button>
    </form>
    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $vehicle->id }}">
        Reject
    </button>
@endif
                    <td>
                        <a href="{{ route('admin.vehicles.show', $vehicle) }}" class="btn btn-sm btn-info" title="View">
                            <i class="fas fa-eye"></i>
                        </a>

                        @if(!$vehicle->is_approved)
                            <form action="{{ route('admin.vehicles.approve', $vehicle) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>

                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $vehicle->id }}" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif

                        <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this vehicle?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Reject Modal -->
                <div class="modal fade" id="rejectModal{{ $vehicle->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('admin.vehicles.reject', $vehicle) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Vehicle #{{ $vehicle->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Reason for rejection</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Reject</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </tbody>
        </table>
        {{ $vehicles->links() }}
    </div>
</div>
@endsection