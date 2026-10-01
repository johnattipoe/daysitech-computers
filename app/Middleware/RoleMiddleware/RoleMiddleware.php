<?php

namespace App\Middleware;

class RoleMiddleware
{
    /** @param string[] $roles */
    public static function handle(array $roles): void
    {
        if (!is_logged_in()) {
            redirect('/login');
        }

        if (!in_array(user_role(), $roles, true)) {
            http_response_code(403);
            require resolve_php_file(base_path('views/pages'), '403');
            exit;
        }
    }
}
