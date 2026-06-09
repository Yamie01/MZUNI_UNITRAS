<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // Trust proxies and handles CORS
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        
        // Maintenance and request validation
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        
        // Input sanitization
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            // Cookie and session handling
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            
            // Security
            \App\Http\Middleware\VerifyCsrfToken::class,
            
            // Route binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Rate limiting
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            
            // Route binding
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        // Authentication
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        
        // Authorization
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        
        // Guest control
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        
        // Security
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Http\Middleware\ValidateSignature::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        
        // Request handling
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'precognitive' => \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
        
        // Cache
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        
        // Custom Role-Based Middleware
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'vehicle.owner' => \App\Http\Middleware\VehicleOwnerMiddleware::class,
        'student' => \App\Http\Middleware\StudentMiddleware::class,
        'staff' => \App\Http\Middleware\StaffMiddleware::class,
    ];
}