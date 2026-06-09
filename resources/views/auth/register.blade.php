<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Mzuni UNITRAS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #00529b 0%, #003f75 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 40px 0;
        }
        .register-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            padding: 0 20px;
        }
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .logo-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #00529b 0%, #003f75 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 5px 15px rgba(0,82,155,0.3);
        }
        .logo-icon i {
            font-size: 32px;
            color: white;
        }
        .logo-img {
            max-height: 60px;
            margin-bottom: 15px;
        }
        .logo-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 5px;
        }
        .logo-subtitle {
            font-size: 12px;
            color: #666;
            letter-spacing: 1px;
        }
        .info-banner {
            background: #e8f0fe;
            border-left: 4px solid #00529b;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 13px;
            color: #00529b;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }
        .form-control, .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #00529b;
            box-shadow: 0 0 0 3px rgba(0,82,155,0.1);
        }
        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #00529b 0%, #003f75 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,82,155,0.3);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .login-link a {
            color: #00529b;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .error-text {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
        }
        .alert-danger {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .checkbox-group input {
            width: 16px;
            height: 16px;
        }
        .checkbox-group label {
            font-size: 14px;
            color: #666;
        }
        .text-link {
            color: #00529b;
            text-decoration: none;
        }
        .text-link:hover {
            text-decoration: underline;
        }
        .hidden {
            display: none;
        }
        .text-muted {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        .copyright {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255,255,255,0.8);
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <!-- Logo Section -->
            <div class="logo-section">
                @if(file_exists(public_path('images/mzuni-logo.png')))
                    <img src="{{ asset('images/mzuni-logo.png') }}" alt="Mzuni UNITRAS" class="logo-img">
                @else
                    <div class="logo-icon">
                        <i class="fas fa-university"></i>
                    </div>
                @endif
                <div class="logo-title">Mzuni UNITRAS</div>
                <div class="logo-subtitle">Mzuzu University - Unified Transport System</div>
            </div>

            {{-- ✅ Show redirect info if applicable --}}
            @php
                $redirectTo = request()->query('redirect_to');
            @endphp
            @if($redirectTo && (str_contains($redirectTo, '/book/') || str_contains($redirectTo, '/bikes/')))
                <div class="info-banner">
                    <i class="fas fa-info-circle me-2"></i>
                    After registration, you will continue with your booking.
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-danger">
                    @foreach ($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                {{-- ✅ Preserve redirect_to parameter --}}
                @if(request()->has('redirect_to'))
                    <input type="hidden" name="redirect_to" value="{{ request()->query('redirect_to') }}">
                @endif

                <div class="form-group">
                    <label class="form-label">Full name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="John Doe" required>
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="your@email.com" required>
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Phone number</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="+265 888 123 456" required>
                    @error('phone') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">I am a</label>
                    <select name="user_type" id="user_type" class="form-select" required>
                        <option value="" disabled {{ old('user_type') ? '' : 'selected' }}>Select user type</option>
                        <option value="student" {{ old('user_type') == 'student' ? 'selected' : '' }}>Student</option>
                        <option value="staff" {{ old('user_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="vehicle_owner" {{ old('user_type') == 'vehicle_owner' ? 'selected' : '' }}>Vehicle Owner</option>
                    </select>
                    @error('user_type') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <!-- Student/Staff Fields -->
                <div id="studentStaffFields">
                    <div class="form-group">
                        <label class="form-label">University/Staff ID</label>
                        <input type="text" name="university_id" value="{{ old('university_id') }}" class="form-control" placeholder="e.g., MZUNI/2023/12345">
                        @error('university_id') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Department/Faculty</label>
                        <input type="text" name="department" value="{{ old('department') }}" class="form-control" placeholder="e.g., Computer Science">
                        @error('department') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Vehicle Owner Fields -->
                <div id="ownerFields" class="hidden">
                    <div class="form-group">
                        <label class="form-label">Driving license number</label>
                        <input type="text" name="driving_license" value="{{ old('driving_license') }}" class="form-control" placeholder="Enter your driving license number">
                        @error('driving_license') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">License expiry date</label>
                        <input type="date" name="license_expiry" value="{{ old('license_expiry') }}" class="form-control">
                        @error('license_expiry') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="********" required>
                    <div class="text-muted">Must be at least 8 characters</div>
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="********" required>
                    @error('password_confirmation') <div class="error-text">{{ $message }}</div> @enderror
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">I agree to the <a href="#" onclick="openModal('termsModal')" class="text-link">Terms</a> and <a href="#" onclick="openModal('privacyModal')" class="text-link">Privacy Policy</a></label>
                </div>
                @error('terms') <div class="error-text">{{ $message }}</div> @enderror

                <button type="submit" class="btn-register">Create account</button>

               <div class="login-link">
                    Already have an account? 
                    <a href="{{ route('login') }}@if(request()->has('redirect_to'))?redirect_to={{ request()->query('redirect_to') }}@endif">
                        Sign in
                    </a>
                </div>
            </form>
        </div>
        <div class="copyright">
            &copy; {{ date('Y') }} Mzuzu University. All rights reserved.
        </div>
    </div>

    <!-- Terms Modal -->
    <div id="termsModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; max-width: 400px; margin: 100px auto; padding: 25px; border-radius: 12px;">
            <h3 style="margin-bottom: 15px;">Terms and Conditions</h3>
            <p>1. You must provide accurate information during registration.</p>
            <p>2. Vehicle owners must ensure vehicles are properly insured.</p>
            <p>3. Students and staff must use valid university identification.</p>
            <p>4. All payments must be made through approved methods.</p>
            <button onclick="closeModal('termsModal')" style="margin-top: 20px; padding: 10px 20px; background: #00529b; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button>
        </div>
    </div>

    <!-- Privacy Modal -->
    <div id="privacyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="background: white; max-width: 400px; margin: 100px auto; padding: 25px; border-radius: 12px;">
            <h3 style="margin-bottom: 15px;">Privacy Policy</h3>
            <p>We collect and store your personal information to provide services.</p>
            <p>Your data will not be shared without your consent.</p>
            <p>You can request deletion of your account at any time.</p>
            <button onclick="closeModal('privacyModal')" style="margin-top: 20px; padding: 10px 20px; background: #00529b; color: white; border: none; border-radius: 6px; cursor: pointer;">Close</button>
        </div>
    </div>

    <script>
        function toggleConditionalFields() {
            const userType = document.getElementById('user_type').value;
            const studentStaffFields = document.getElementById('studentStaffFields');
            const ownerFields = document.getElementById('ownerFields');
            
            const isStudentStaff = userType === 'student' || userType === 'staff';
            const isOwner = userType === 'vehicle_owner';
            
            if (studentStaffFields) studentStaffFields.style.display = isStudentStaff ? 'block' : 'none';
            if (ownerFields) ownerFields.style.display = isOwner ? 'block' : 'none';
        }
        
        function openModal(id) {
            document.getElementById(id).style.display = 'block';
        }
        
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        const userTypeSelect = document.getElementById('user_type');
        if (userTypeSelect) {
            userTypeSelect.addEventListener('change', toggleConditionalFields);
            toggleConditionalFields();
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.style.display === 'block' && event.target.className === '') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>