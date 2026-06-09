<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Mzuni UNITRAS')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <div class="bg-white rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-bus text-primary fs-6"></i>
                </div>
                <div>
                    <span class="fw-semibold">Mzuni UNITRAS</span>
                    <small class="d-block" style="font-size: 0.6rem;">Mzuzu University</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2" href="{{ route('register') }}">Sign Up</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <a class="dropdown-item" href="{{ route('subscription.index') }}">
        <i class="fas fa-ticket-alt me-2"></i> My Subscription
    </a>
    <!-- Main Content -->
    <main style="margin-top: 70px;">
        @yield('content')
    </main>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

     <!--<div class="col-md-4 mb-3">
                    <h6>Contact</h6>
                    <p class="small text-muted mb-1"><i class="fas fa-envelope me-2"></i>unitras@mzuni.ac.mw</p>
                    <p class="small text-muted"><i class="fas fa-phone me-2"></i>+265 990 179 811</p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="text-center small text-muted">
                &copy; {{ date('Y') }} Mzuni UNITRAS. All rights reserved.
            </div>-->
</body>
</html>