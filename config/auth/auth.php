<?php
/**
 * Authentication configuration.
 */

return [
    'session_key'      => 'daysitech_user',
    'session_name'     => env('SESSION_NAME', 'daysitech_session'),
    'session_lifetime' => (int) env('SESSION_LIFETIME', 120), // minutes

    // Password rules enforced in Helpers/validation.php
    'password_min_length' => 8,

    // Roles recognised across the app
    'roles' => [
        'customer' => 'customer',
        'staff'    => 'staff',
        'admin'    => 'admin',
    ],

    'default_role' => 'customer',

    // Where to send users after login, per role
    'redirects' => [
        'customer' => '/account/dashboard',
        'staff'    => '/admin/dashboard',
        'admin'    => '/admin/dashboard',
    ],

    'login_route'  => '/login',
    'guest_only_routes_redirect' => '/account/dashboard',
];
