@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Profile</h2>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
    <hr>
    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Delete your account? This cannot be undone.')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete Account</button>
    </form>
</div>
@endsection