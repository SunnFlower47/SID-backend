<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => env('CORS_ALLOWED_ORIGINS')
        ? explode(',', env('CORS_ALLOWED_ORIGINS'))
        : [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://localhost:3030',
            'http://127.0.0.1:3000',
            'http://sistem-desa-cibatu.test',
            'https://pemdescibatu2001.online',
            'https://api-vilage.sunnflower.site',
            'https://vilage.sunnflower.site',
            'https://web-desa-sid.vercel.app',
            'https://cibatu-vibe-ai-505268805663.asia-southeast2.run.app/'
        ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'X-API-Key',
        'X-Recaptcha-Token',
        'X-Recaptcha-V3-Token',
        'X-Timestamp',
        'X-Signature',
        'Accept',
        'Authorization',
        'Origin',
        'X-Requested-With',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
