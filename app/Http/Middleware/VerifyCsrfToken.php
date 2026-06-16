<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'bike-rental/webhook',
    ];
}

/*
<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        '/payment/webhook',
        'bike-rental/webhook',
        '/bike-rental/webhook',
    ];

    // Optional: disable CSRF cookie for this route (double safety)
    protected function shouldAddXsrfTokenCookie()
    {
        $request = request();
        if ($request->is('bike-rental/webhook') || $request->is('/bike-rental/webhook')) {
            return false;
        }
        return parent::shouldAddXsrfTokenCookie();
    }
}
    */