<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <i class="fas fa-bus me-2 fs-4"></i>
            <div>
                <span class="fw-bold fs-5">Mzuni UNITRAS</span>
                <small class="d-block" style="font-size: 0.65rem;">Mzuzu University</small>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('search') ? 'active' : '' }}" href="{{ route('search') }}">
                        <i class="fas fa-search me-1"></i> Find Rides
                    </a>
                </li>
                
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm ms-2" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> 
                            {{-- FIXED: Check if user exists before accessing name --}}
                            {{ Auth::user()->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                                </a>
                            </li>
                            
                            @if(Auth::user() && Auth::user()->user_type === 'vehicle_owner')
                            <li>
                                <a class="dropdown-item" href="{{ route('vehicle-owner.dashboard') }}">
                                    <i class="fas fa-car me-2"></i> Owner Dashboard
                                </a>
                            </li>
                            @endif
                            
                            @if(Auth::user() && Auth::user()->user_type === 'admin')
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-crown me-2"></i> Admin Panel
                                </a>
                            </li>
                            @endif
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>