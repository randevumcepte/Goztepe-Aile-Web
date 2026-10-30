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

    // --- Göztepe Tribünleri ---

    // Ödeme sağlayıcısı: fake | iyzico
    'payment' => [
        'driver' => env('PAYMENT_DRIVER', 'fake'),
    ],

    'iyzico' => [
        'api_key' => env('IYZICO_API_KEY'),
        'secret_key' => env('IYZICO_SECRET_KEY'),
        'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
    ],

    // Push bildirim
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'), // eski API (kapandı), kullanılmıyor
        // FCM v1 servis hesabı JSON yolu (Firebase → Proje ayarları → Hizmet hesapları)
        'credentials' => env('FCM_CREDENTIALS', storage_path('app/fcm-service-account.json')),
    ],

    'onesignal' => [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
    ],

    // Maç analizi önizleme metni (Haiku). Boşsa kural tabanlı metin kullanılır.
    'anthropic' => [
        'key' => env('ANTHROPIC_API_KEY'),
    ],

];
