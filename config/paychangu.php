<?php

return [
    'private_key' => env('PAYCHANGU_API_PRIVATE_KEY'),
    'public_key' => env('PAYCHANGU_PUBLIC_KEY'),
    'base_url' => env('PAYCHANGU_API_BASE_URL', 'https://api.paychangu.com/'),
    'callback_url' => env('PAYCHANGU_CALLBACK_URL'),
    'return_url' => env('PAYCHANGU_RETURN_URL'),
    'webhook_secret' => env('PAYCHANGU_WEBHOOK_SECRET'),
    'currency'      => env('PAYCHANGU_CURRENCY', 'MWK'),
];

/*<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayChangu API Private Key
    |--------------------------------------------------------------------------
    |
    | This is the private key used to authenticate with the PayChangu API.
    |
    */
    //'private_key' => env('PAYCHANGU_API_PRIVATE_KEY'), // @phpstan-ignore-line

    /*
    |--------------------------------------------------------------------------
    | PayChangu API Base URL
    |--------------------------------------------------------------------------
    |
    | This is the root URL for the PayChangu API.
    | Specific endpoints (checkout, mobile-money) will be constructed from this.
    |
    */
    //'api_base_url' => env('PAYCHANGU_API_BASE_URL', 'https://api.paychangu.com/'), // @phpstan-ignore-line
//];
