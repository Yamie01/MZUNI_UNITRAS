<?php

return [
    // ... other services

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'mobile_money' => [
        'mpesa' => [
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'shortcode' => env('MPESA_SHORTCODE'),
            'passkey' => env('MPESA_PASSKEY'),
        ],
        'airtel' => [
            'client_id' => env('AIRTEL_CLIENT_ID'),
            'client_secret' => env('AIRTEL_CLIENT_SECRET'),
        ],
    ],

    'africastalking' => [
    'username' => env('AFRICASTALKING_USERNAME'),
    'api_key' => env('AFRICASTALKING_API_KEY'),
    'from' => env('AFRICASTALKING_FROM'),
],

<<<<<<< HEAD
=======
'paychangu' => [
        'private_key' => env('PAYCHANGU_API_PRIVATE_KEY'),
        'public_key' => env('PAYCHANGU_PUBLIC_KEY'),
        'base_url' => env('PAYCHANGU_API_BASE_URL', 'https://api.paychangu.com/'),
        'callback_url' => env('PAYCHANGU_CALLBACK_URL'),
        'return_url' => env('PAYCHANGU_RETURN_URL'),
        'webhook_secret' => env('PAYCHANGU_WEBHOOK_SECRET'),
        'currency' => env('PAYCHANGU_CURRENCY', 'MWK'),
    ],
    
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
'app' => [
    'admin_phone' => env('ADMIN_PHONE', '+265990179811'),
],
];