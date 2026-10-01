<?php
/**
 * Security helpers: CSRF protection, input sanitisation, basic rate limiting.
 */

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(?string $token): bool
    {
        return !empty($token) && !empty($_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $token);
    }
}

if (!function_exists('require_csrf')) {
    function require_csrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!verify_csrf($token)) {
            http_response_code(419);
            if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')) {
                json_response(['success' => false, 'message' => 'Your session expired. Please refresh and try again.'], 419);
            }
            flash('error', 'Your session expired. Please try again.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
}

if (!function_exists('sanitize')) {
    function sanitize($value)
    {
        if (is_array($value)) {
            return array_map('sanitize', $value);
        }
        return is_string($value) ? trim(strip_tags($value)) : $value;
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input(array $input): array
    {
        return sanitize($input);
    }
}

if (!function_exists('hash_password')) {
    function hash_password(string $plain): string
    {
        return password_hash($plain, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}

if (!function_exists('verify_password')) {
    function verify_password(string $plain, string $hash): bool
    {
        return password_verify($plain, $hash);
    }
}

if (!function_exists('rate_limit')) {
    /**
     * Very small file-free rate limiter using the session.
     * Returns true if the action is allowed, false if throttled.
     */
    function rate_limit(string $key, int $maxAttempts = 5, int $decaySeconds = 300): bool
    {
        $bucket = $_SESSION['_rate_limit'][$key] ?? ['count' => 0, 'reset' => time() + $decaySeconds];

        if (time() > $bucket['reset']) {
            $bucket = ['count' => 0, 'reset' => time() + $decaySeconds];
        }

        $bucket['count']++;
        $_SESSION['_rate_limit'][$key] = $bucket;

        return $bucket['count'] <= $maxAttempts;
    }
}

if (!function_exists('secure_filename')) {
    function secure_filename(string $original): string
    {
        $ext = pathinfo($original, PATHINFO_EXTENSION);
        return bin2hex(random_bytes(8)) . '.' . strtolower($ext);
    }
}
