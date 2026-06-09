@extends('layouts.app')

@section('title', 'My Profile - Mzuni UNITRAS')

@push('styles')
<style>
    .profile-card { background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .avatar-placeholder { width: 100px; height: 100px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; margin: 0 auto 1rem; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="profile-card">
                <div class="text-center">
                    <div class="avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>{{ Auth::user()->name }}</h3>
                    <p class="text-muted">{{ ucfirst(Auth::user()->user_type) }}</p>
                </div>
                <hr>

                <!-- Update Profile Info -->
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', Auth::user()->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>

                <hr>

                <!-- Change Password -->
                <form method="POST" action="{{ route('profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <h5 class="fw-bold">Change Password</h5>
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-secondary">Change Password</button>
                </form>

                <hr>

                <!-- Delete Account -->
                <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action is permanent.');">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label text-danger">Delete Account</label>
                        <p class="small text-muted">Once deleted, all your data will be permanently removed.</p>
                        <button type="submit" class="btn btn-danger">Delete My Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection