@extends('layouts.app')

@section('title', 'Login - Mzuni UNITRAS')

@section('content')
<div class="login-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <!-- Logo -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni University" height="60" class="mb-2">
                        <h4 class="fw-bold text-mzuni-green">Mzuni UNITRAS</h4>
                        <p class="text-muted small">Mzuzu University - Unified Transport System</p>
                    </div>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email address</label>
                            <input id="email" type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" value="{{ old('email') }}" 
                                placeholder="your.email@mzuni.ac.mw" required autofocus>
                            @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="position-relative">
                                <input id="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" placeholder="Enter your password" required>
                                <button type="button" class="btn btn-link position-absolute end-0 top-0 text-muted" 
                                    onclick="togglePassword()" style="padding: 0.6rem 1rem;">
                                    <i class="fas fa-eye" id="passwordToggle"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="small text-mzuni-green">Forgot password?</a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign in
                        </button>
                    </form>

                    <!-- Register Link -->
                    <div class="text-center mt-3">
                        <p class="small text-muted">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="text-mzuni-green fw-semibold">Create account</a>
                        </p>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted mb-0">&copy; {{ date('Y') }} Mzuzu University. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* ===== LOGIN PAGE STYLES ===== */
    .login-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #00693E 0%, #004d2e 50%, #003d23 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Background decoration */
    .login-page::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
        pointer-events: none;
    }

    .login-page::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 215, 0, 0.04);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Login Card */
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2.5rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        z-index: 1;
    }

    .login-card .text-mzuni-green {
        color: #00693E;
    }

    .login-card .form-control {
        border-radius: 12px;
        border: 2px solid #e9ecef;
        padding: 0.7rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .login-card .form-control:focus {
        border-color: #00693E;
        box-shadow: 0 0 0 4px rgba(0, 105, 62, 0.12);
    }

    .login-card .btn-primary {
        background: #00693E;
        border-color: #00693E;
        border-radius: 12px;
        padding: 0.8rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .login-card .btn-primary:hover {
        background: #004d2e;
        border-color: #004d2e;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 105, 62, 0.3);
    }

    .login-card .btn-primary:active {
        transform: scale(0.97);
    }

    .login-card a.text-mzuni-green {
        color: #00693E;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .login-card a.text-mzuni-green:hover {
        color: #004d2e;
        text-decoration: underline;
    }

    .login-card .form-check-input:checked {
        background-color: #00693E;
        border-color: #00693E;
    }

    /* Password toggle */
    .login-card .btn-link {
        text-decoration: none;
        color: #888;
    }

    .login-card .btn-link:hover {
        color: #555;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .login-card {
            padding: 1.5rem;
            margin: 1rem;
            border-radius: 16px;
        }

        .login-card h4 {
            font-size: 1.3rem;
        }

        .login-card img {
            height: 45px;
        }
    }
</style>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const toggle = document.getElementById('passwordToggle');
        if (password.type === 'password') {
            password.type = 'text';
            toggle.classList.remove('fa-eye');
            toggle.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            toggle.classList.remove('fa-eye-slash');
            toggle.classList.add('fa-eye');
        }
    }
</script>
@endsection