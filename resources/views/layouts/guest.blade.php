<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Mzuni UNITRAS</title>
    
    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS CDN (if needed) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Additional custom styles */
        body {
            background-color: #f3f4f6;
        }
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #2563eb;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div>
        {{ $slot }}
    </div>
    <div class="login-footer">
            &copy; {{ date('Y') }} Mzuzu University. All rights reserved.
        </div>
    <!-- Bootstrap JS (optional, for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>