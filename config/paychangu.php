<?php

return [
    'private_key' => env('PAYCHANGU_API_PRIVATE_KEY'),
    'public_key' => env('PAYCHANGU_PUBLIC_KEY'),
    'api_base_url' => env('PAYCHANGU_API_BASE_URL', 'https://api.paychangu.com/'),
    'callback_url' => env('PAYCHANGU_CALLBACK_URL'),
    'return_url' => env('PAYCHANGU_RETURN_URL'),
    'webhook_secret' => env('PAYCHANGU_WEBHOOK_SECRET'),
    'currency' => env('PAYCHANGU_CURRENCY', 'MWK'),
];