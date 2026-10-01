<?php

namespace App\Middleware;

class AuthMiddleware
{
    public static function handle(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Please sign in to continue.');
            $_SESSION['_intended'] = $_SERVER['REQUEST_URI'] ?? '/';
            redirect(config('auth.login_route'));
        }
    }
}
