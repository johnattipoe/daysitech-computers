<?php
/**
 * Core application configuration.
 * Values are pulled from environment variables (.env) with sane fallbacks.
 */

return [
    'name'       => env('APP_NAME', 'Daysitech Computers'),
    'env'        => env('APP_ENV', 'local'),
    'debug'      => filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    'url'        => rtrim(env('APP_URL', 'http://localhost:8000'), '/'),
    'timezone'   => env('APP_TIMEZONE', 'Africa/Accra'),
    'locale'     => env('APP_LOCALE', 'en'),
    'currency'   => env('APP_CURRENCY', 'GHS'),
    'currency_symbol' => env('APP_CURRENCY_SYMBOL', 'GH₵'),
    'key'        => env('APP_KEY', ''),

    // Business identity — used across views, invoices, emails
    'business' => [
        'name'      => 'Daysitech Computers',
        'tagline'   => 'Sales · Repairs · IT Solutions',
        'phone'     => '+233 24 000 0000',
        'whatsapp'  => '+233 24 000 0000',
        'email'     => 'info@daysitech.com',
        'address'   => 'Accra, Ghana',
        'hours'     => 'Mon – Sat: 8:00am – 7:00pm',
        'socials'   => [
            'facebook'  => '#',
            'instagram' => '#',
            'twitter'   => '#',
        ],
    ],

    // Pagination defaults
    'pagination' => [
        'products_per_page'  => 12,
        'orders_per_page'    => 15,
        'repairs_per_page'   => 15,
        'reviews_per_page'   => 10,
    ],

    // Feature toggles
    'features' => [
        'reviews_require_approval' => true,
        'guest_checkout'           => true,
        'repair_sms_notifications' => false,
    ],
];
