@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Dashboard</h1>
    <p>Welcome, {{ $user->name }}!</p>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Find Rides</h5>
                    <p class="card-text">Search for available transportation</p>
                    <a href="{{ route('search') }}" class="btn btn-primary">Search</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Bookings</h5>
                    <p class="card-text">View your booking history</p>
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-primary">View</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">My Profile</h5>
                    <p class="card-text">Manage your account settings</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection