@extends('layouts.admin')

@section('title', 'Manage Advertisements - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Ads</h6>
                    <h4>{{ $counts['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h4>{{ $counts['pending'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Approved</h6>
                    <h4>{{ $counts['approved'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h6>Rejected</h6>
                    <h4>{{ $counts['rejected'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Active</h6>
                    <h4>{{ $counts['active'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.advertisements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search title or location" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="ride_share" {{ request('type') == 'ride_share' ? 'selected' : '' }}>Ride Share</option>
                        <option value="taxi" {{ request('type') == 'taxi' ? 'selected' : '' }}>Taxi</option>
                        <option value="bus" {{ request('type') == 'bus' ? 'selected' : '' }}>Bus</option>
                        <option value="bike_share" {{ request('type') == 'bike_share' ? 'selected' : '' }}>Bike Share</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-info" id="bulkApproveBtn" style="display: none;">Bulk Approve</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Advertisements Table -->
    <div class="card">
        <div class="card-header">
            <h5>All Advertisements</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="adsTable">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Vehicle</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($advertisements as $ad)
                    <tr>
                        <td><input type="checkbox" class="ad-checkbox" value="{{ $ad->id }}" data-status="{{ $ad->status }}"></td>
                        <td>{{ $ad->id }}</td>
                        <td>{{ Str::limit($ad->title, 30) }}</td>
                        <td>{{ $ad->owner->name ?? 'N/A' }}</td>
                        <td>{{ $ad->vehicle->registration_number ?? 'N/A' }}</td>
                        <td>{{ $ad->from_location }} → {{ $ad->to_location }}</td>
                        <td>{{ $ad->departure_time->format('d M Y H:i') }}</td>
                        <td>MWK {{ number_format($ad->price, 2) }}</td>
                        <td>
                            @if($ad->status == 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($ad->status == 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($ad->status == 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.advertisements.show', $ad) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($ad->status == 'pending')
                                <form action="{{ route('admin.advertisements.approve', $ad) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $ad->id }}">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                            <form action="{{ route('admin.advertisements.destroy', $ad) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this advertisement?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal{{ $ad->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.advertisements.reject', $ad) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject Advertisement #{{ $ad->id }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Reason for rejection..." required></textarea>
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
            {{ $advertisements->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.ad-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkApproveButton();
    });

    // Toggle bulk approve button based on selected pending ads
    document.querySelectorAll('.ad-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkApproveButton);
    });

    function toggleBulkApproveButton() {
        let selected = document.querySelectorAll('.ad-checkbox:checked');
        let pendingSelected = Array.from(selected).filter(cb => cb.dataset.status === 'pending');
        let btn = document.getElementById('bulkApproveBtn');
        if (pendingSelected.length > 0) {
            btn.style.display = 'inline-block';
            btn.onclick = function() {
                let ids = pendingSelected.map(cb => cb.value);
                if (confirm('Approve ' + ids.length + ' selected advertisements?')) {
                    fetch('{{ route("admin.advertisements.bulk-approve") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: ids })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error approving advertisements.');
                        }
                    });
                }
            };
        } else {
            btn.style.display = 'none';
        }
    }
</script>
@endpush