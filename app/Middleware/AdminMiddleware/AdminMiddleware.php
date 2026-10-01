<?php

namespace App\Middleware;

class AdminMiddleware
{
    public static function handle(): void
    {
        if (!is_logged_in()) {
            flash('error', 'Please sign in to access the admin panel.');
            redirect('/admin/login');
        }

        if (!is_admin()) {
            http_response_code(403);
            require resolve_php_file(base_path('views/pages'), '403');
            exit;
        }
    }
}
