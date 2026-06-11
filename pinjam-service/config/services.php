<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Microservice URLs - PinjamService
    |--------------------------------------------------------------------------
    |
    | URL service lain untuk komunikasi antar service.
    | PinjamService memanggil: MemberService, BukuService
    |
    */

    'member_service' => [
        'url' => env('MEMBER_SERVICE_URL', 'http://127.0.0.1:8001'),
    ],

    'buku_service' => [
        'url' => env('BUKU_SERVICE_URL', 'http://127.0.0.1:8002'),
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
