<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Mzuni UNITRAS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #00529b 0%, #003f75 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { max-width: 450px; width: 100%; margin: 20px; }
        .login-card { background: white; border-radius: 12px; box-shadow: 0 20px 35px rgba(0,0,0,0.2); padding: 40px; }
        .logo-section { text-align: center; margin-bottom: 30px; }
        .logo-img { max-height: 60px; margin-bottom: 15px; }
        .logo-title { font-size: 24px; font-weight: 700; color: #1a1a2e; margin-bottom: 5px; }
        .logo-subtitle { font-size: 12px; color: #666; letter-spacing: 1px; }
        .info-banner { background: #e8f0fe; border-left: 4px solid #00529b; padding: 10px 15px; margin-bottom: 20px; border-radius: 8px; font-size: 13px; color: #00529b; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #333; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; }
        .form-control:focus { outline: none; border-color: #00529b; box-shadow: 0 0 0 3px rgba(0,82,155,0.1); }
        .btn-login { width: 100%; padding: 12px; background: linear-gradient(135deg, #00529b 0%, #003f75 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,82,155,0.3); }
        .remember-forgot { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #666; }
        .forgot-link { font-size: 14px; color: #00529b; text-decoration: none; }
        .register-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .register-link a { color: #00529b; text-decoration: none; font-weight: 500; }
        .copyright { text-align: center; margin-top: 20px; font-size: 12px; color: rgba(255,255,255,0.8); }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni UNITRAS" class="logo-img" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%2300529b%22/%3E%3Ctext x=%2250%22 y=%2250%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22white%22 font-size=%2240%22%3EMU%3C/text%3E%3C/svg%3E'">
                <div class="logo-title">Mzuni UNITRAS</div>
                <div class="logo-subtitle">Mzuzu University - Unified Transport System</div>
            </div>

            {{-- Show redirect info --}}
            @php
                $redirectTo = request()->query('redirect_to');
            @endphp
            @if($redirectTo)
                <div class="info-banner">
                    <i class="fas fa-info-circle me-2"></i>
                    After login, you will continue with your {{ str_contains($redirectTo, 'book') ? 'ride booking' : 'bike rental' }}.
                </div>
            @endif

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                {{-- ✅ CRITICAL: Preserve redirect_to parameter --}}
                @if($redirectTo)
                    <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
                @endif

                <div class="form-group">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="remember-forgot">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Sign in</button>

                <div class="register-link">
                    Don't have an account? 
                    <a href="{{ route('register') }}@if(request()->has('redirect_to'))?redirect_to={{ request()->query('redirect_to') }}@endif">
                        Create account
                    </a>
                </div>
            </form>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} Mzuzu University. All rights reserved.
        </div>
    </div>
</body>
</html>