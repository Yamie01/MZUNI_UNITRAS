<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayChangu API Configuration
    |--------------------------------------------------------------------------
    */
    'api_key' => env('PAYCHANGU_API_PRIVATE_KEY'),
    'public_key' => env('PAYCHANGU_PUBLIC_KEY'),
    'base_url' => env('PAYCHANGU_API_BASE_URL', 'https://api.paychangu.com'),
    'webhook_secret' => env('PAYCHANGU_WEBHOOK_SECRET'),
    'currency' => env('PAYCHANGU_CURRENCY', 'MWK'),
];