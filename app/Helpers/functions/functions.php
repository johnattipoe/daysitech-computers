<?php
/**
 * Global helper functions used throughout the application.
 */

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return rtrim(dirname(__DIR__, 3), '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('env')) {
    /**
     * Read a value from the environment (populated by vlucas/phpdotenv or the
     * lightweight fallback loader in index.php).
     */
    function env(string $key, $default = null)
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }
        return match (strtolower((string) $value)) {
            'true', '(true)'   => true,
            'false', '(false)' => false,
            'null', '(null)'   => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }
}

if (!function_exists('config')) {
    function resolve_php_file(string $directory, string $path): string
    {
        $path = ltrim($path, '/');
        $flat = rtrim($directory, '/') . '/' . $path . '.php';
        if (file_exists($flat)) return $flat;

        $name = basename($path);
        return rtrim($directory, '/') . '/' . $path . '/' . $name . '.php';
    }

    /**
     * Dot-notation config accessor, e.g. config('app.business.phone').
     */
    function config(string $key, $default = null)
    {
        static $cache = [];
        [$file, $rest] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset($cache[$file])) {
            $path = resolve_php_file(base_path('config'), $file);
            $cache[$file] = file_exists($path) ? require $path : [];
        }

        if ($rest === null) {
            return $cache[$file] ?: $default;
        }

        $value = $cache[$file];
        foreach (explode('.', $rest) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return config('app.url') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return config('app.url') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void
    {
        header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
        exit;
    }
}

if (!function_exists('view')) {
    /**
     * Render a view file from /views with the given data, wrapped in the
     * main layout unless $layout = null (used for AJAX partials).
     */
    function view(string $view, array $data = [], ?string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = resolve_php_file(base_path('views'), str_replace('.', '/', $view));

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View not found: {$view}";
            return;
        }

        if ($layout === null) {
            require $viewFile;
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require resolve_php_file(base_path('views/layouts'), $layout);
    }
}

if (!function_exists('admin_view')) {
    function admin_view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = resolve_php_file(base_path('admin'), str_replace('.', '/', $view));

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "Admin view not found: {$view}";
            return;
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require resolve_php_file(base_path('admin'), 'layout');
    }
}

if (!function_exists('json_response')) {
    function json_response(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('current_user')) {
    function current_user(): ?array
    {
        $sessionKey = config('auth.session_key');
        $user = $_SESSION[$sessionKey] ?? null;

        if (!is_array($user) || !is_string($user['id'] ?? null) || trim($user['id']) === '') {
            unset($_SESSION[$sessionKey]);
            return null;
        }

        return $user;
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return current_user() !== null;
    }
}

if (!function_exists('user_role')) {
    function user_role(): ?string
    {
        return current_user()['role'] ?? null;
    }
}

if (!function_exists('is_admin')) {
    function is_admin(): bool
    {
        return in_array(user_role(), ['admin', 'staff'], true);
    }
}

if (!function_exists('flash')) {
    /**
     * Set a flash message: flash('success', 'Order placed!')
     * Read + clear a flash message: flash('success')
     */
    function flash(string $type, ?string $message = null)
    {
        if ($message !== null) {
            $_SESSION['_flash'][$type] = $message;
            return null;
        }
        $value = $_SESSION['_flash'][$type] ?? null;
        unset($_SESSION['_flash'][$type]);
        return $value;
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        $value = $_SESSION['_old'][$key] ?? $default;
        return $value;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_csrf" value="' . e($token) . '">';
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$vars): void
    {
        echo '<pre style="background:#111;color:#0f0;padding:1rem;">';
        foreach ($vars as $v) {
            print_r($v);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('log_message')) {
    function log_message(string $level, string $message): void
    {
        $dir = base_path('storage/logs');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $line = sprintf("[%s] %s: %s\n", date('Y-m-d H:i:s'), strtoupper($level), $message);
        file_put_contents($dir . '/app.log', $line, FILE_APPEND);
    }
}

if (!function_exists('array_get')) {
    function array_get(array $arr, string $key, $default = null)
    {
        return $arr[$key] ?? $default;
    }
}

if (!function_exists('uuid')) {
    function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}

if (!function_exists('generate_order_number')) {
    function generate_order_number(): string
    {
        return 'DTC-ORD-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    }
}

if (!function_exists('generate_repair_ticket')) {
    function generate_repair_ticket(): string
    {
        return 'DTC-RPR-' . date('ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
    }
}
