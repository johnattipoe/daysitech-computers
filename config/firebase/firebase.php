<?php
/**
 * Firebase configuration.
 * Used by App\Services\FirebaseService to talk to Firestore (REST API)
 * and Firebase Authentication (Identity Toolkit REST API).
 */

return [
    'project_id'   => env('FIREBASE_PROJECT_ID'),
    'api_key'      => env('FIREBASE_API_KEY'),
    'auth_domain'  => env('FIREBASE_AUTH_DOMAIN'),
    'database_url' => env('FIREBASE_DATABASE_URL'),
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
    'app_id'       => env('FIREBASE_APP_ID'),
    'service_account_path' => base_path(env('FIREBASE_SERVICE_ACCOUNT_PATH', 'storage/firebase-service-account.json')),

    // Base REST endpoints
    'endpoints' => [
        'firestore' => 'https://firestore.googleapis.com/v1/projects/{project}/databases/(default)/documents',
        'identity'  => 'https://identitytoolkit.googleapis.com/v1',
        'secure_token' => 'https://securetoken.googleapis.com/v1/token',
    ],

    // Firestore collection names — keep central so refactors are painless
    'collections' => [
        'users'         => 'users',
        'products'      => 'products',
        'categories'    => 'categories',
        'brands'        => 'brands',
        'orders'        => 'orders',
        'repairs'       => 'repairs',
        'inventory'     => 'inventory_movements',
        'payments'      => 'payments',
        'reviews'       => 'reviews',
        'notifications' => 'notifications',
        'carts'         => 'carts',
    ],
];
