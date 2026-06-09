@extends('layouts.admin')

@section('title', 'User Details - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>User Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone }}</p>
                    <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'danger' }}">{{ ucfirst($user->status) }}</span>
                    </p>
                    <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Vehicles ({{ $user->vehicles->count() }})</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Reg No.</th>
                                <th>Model</th>
                                <th>Type</th>
                                <th>Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->vehicles as $vehicle)
                            <tr>
                                <td>{{ $vehicle->registration_number }}</td>
                                <td>{{ $vehicle->model }}</td>
                                <td>{{ ucfirst($vehicle->vehicle_type) }}</td>
                                <td>
                                    @if($vehicle->is_approved)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-warning">No</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back to Users</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit User</a>
    </div>
</div>
@endsection