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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'recaptcha' => [
        // v2 keys
        'v2_site_key' => env('RECAPTCHA_V2_SITE_KEY'),
        'v2_secret_key' => env('RECAPTCHA_V2_SECRET_KEY'),
        // v3 keys
        'v3_site_key' => env('RECAPTCHA_V3_SITE_KEY'),
        'v3_secret_key' => env('RECAPTCHA_V3_SECRET_KEY'),
        // Legacy (for backward compatibility) - menggunakan v2 sebagai default
        'site_key' => env('RECAPTCHA_SITE_KEY', env('RECAPTCHA_V2_SITE_KEY')),
        'secret_key' => env('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_V2_SECRET_KEY')),
        // Master switch + score threshold v3
        // CATATAN: Jangan nested env() di sini - tidak bekerja di Laravel config.
        // Set RECAPTCHA_ENABLED=true di .env production, RECAPTCHA_ENABLED=false di .env local.
        'enabled' => env('RECAPTCHA_ENABLED', false),
        'v3_min_score' => env('RECAPTCHA_V3_MIN_SCORE', 0.5),
    ],

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    ],

];
