<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for payment gateway integration.
    | The platform supports both iyzico and Stripe payment gateways.
    | Set PAYMENT_GATEWAY environment variable to 'iyzico' or 'stripe'.
    |
    */

    'gateway' => env('PAYMENT_GATEWAY', 'iyzico'),

    /*
    |--------------------------------------------------------------------------
    | iyzico Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for iyzico payment gateway.
    | Use sandbox URL for testing: https://sandbox-api.iyzipay.com
    | Use production URL for live: https://api.iyzipay.com
    |
    */

    'iyzico' => [
        'api_key' => env('IYZICO_API_KEY'),
        'secret_key' => env('IYZICO_SECRET_KEY'),
        'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
        'webhook_secret' => env('IYZICO_WEBHOOK_SECRET'),
        'webhook_strict' => env('IYZICO_WEBHOOK_STRICT', env('APP_ENV') === 'production'),
        'webhook_tolerance' => (int) env('IYZICO_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe payment gateway.
    | Use test keys for development and live keys for production.
    |
    */

    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

];
