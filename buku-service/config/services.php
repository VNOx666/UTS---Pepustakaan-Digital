<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microservice URLs - BukuService
    |--------------------------------------------------------------------------
    |
    | URL service lain untuk komunikasi antar service.
    | BukuService memanggil: PinjamService
    |
    */

    'pinjam_service' => [
        'url' => env('PINJAM_SERVICE_URL', 'http://127.0.0.1:8003'),
    ],

    // Default Laravel services
    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

];
