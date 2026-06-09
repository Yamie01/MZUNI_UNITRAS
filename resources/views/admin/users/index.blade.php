@extends('layouts.admin') {{-- or 'admin.layouts.admin' depending on your structure --}}

@section('title', 'Manage Users - Mzuni UNITRAS')

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h6>Total Users</h6>
                    <h4>{{ $counts['total'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h6>Active</h6>
                    <h4>{{ $counts['active'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h6>Suspended</h6>
                    <h4>{{ $counts['suspended'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h6>Students</h6>
                    <h4>{{ $counts['students'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary">
                <div class="card-body">
                    <h6>Staff</h6>
                    <h4>{{ $counts['staff'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <h6>Owners</h6>
                    <h4>{{ $counts['owners'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search name or email" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ request('type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="vehicle_owner" {{ request('type') == 'vehicle_owner' ? 'selected' : '' }}>Vehicle Owner</option>
                        <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-3 text-end">
                    <a href="{{ route('admin.users.export') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h5>All Users</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>
                            <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'vehicle_owner' ? 'warning' : 'info') }}">
                                {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                            </span>
                        </td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($user->status == 'suspended')
                                <span class="badge bg-danger">Suspended</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->status == 'active')
                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Suspend this user?')">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection