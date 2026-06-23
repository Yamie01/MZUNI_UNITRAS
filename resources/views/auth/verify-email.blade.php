@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope me-2"></i> Verify Your Email</h4>
                </div>
                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> A fresh verification link has been sent to your email address.
                        </div>
                    @endif

                    <p>Before proceeding, please check your email for a verification link.</p>
                    <p class="text-muted">If you did not receive the email,</p>

                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">Click here to request another</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection