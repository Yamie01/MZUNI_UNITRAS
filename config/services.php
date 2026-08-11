<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'paychangu' => [
        'test' => [
            'private_key' => env('PAYCHANGU_TEST_PRIVATE_KEY'),
            'public_key' => env('PAYCHANGU_TEST_PUBLIC_KEY'),
            'base_url' => env('PAYCHANGU_TEST_BASE_URL', 'https://sandbox-api.paychangu.com/'),
        ],
        'live' => [
            'private_key' => env('PAYCHANGU_LIVE_PRIVATE_KEY'),
            'public_key' => env('PAYCHANGU_LIVE_PUBLIC_KEY'),
            'base_url' => env('PAYCHANGU_LIVE_BASE_URL', 'https://api.paychangu.com/'),
        ],
        'mode' => env('PAYCHANGU_MODE', 'test'),
        'callback_url' => env('PAYCHANGU_CALLBACK_URL'),
        'return_url' => env('PAYCHANGU_RETURN_URL'),
        'webhook_secret' => env('PAYCHANGU_WEBHOOK_SECRET'),
        'currency' => env('PAYCHANGU_CURRENCY', 'MWK'),
    ],

];