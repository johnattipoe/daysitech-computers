<?php

declare(strict_types=1);

$apiRoot = dirname(__DIR__);

if (file_exists($apiRoot . '/vendor/autoload.php')) {
    require_once $apiRoot . '/vendor/autoload.php';
}

spl_autoload_register(function ($class) use ($apiRoot) {
    foreach (['App\\Controllers', 'App\\Helpers', 'App\\Middleware', 'App\\Models', 'App\\Services'] as $namespace) {
        $prefix = $namespace . '\\';
        if (str_starts_with($class, $prefix)) {
            $name = substr($class, strlen($prefix));
            $file = $apiRoot . '/app/' . substr($namespace, 4) . '/' . $name . '/' . basename($name) . '.php';
            if (file_exists($file)) require $file;
            return;
        }
    }
});

if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable($apiRoot)->safeLoad();
}

require_once $apiRoot . '/app/Helpers/functions/functions.php';
require_once $apiRoot . '/app/Helpers/security/security.php';
require_once $apiRoot . '/app/Helpers/validation/validation.php';
require_once $apiRoot . '/app/Helpers/format/format.php';
require_once resolve_php_file($apiRoot . '/config', 'constants');

date_default_timezone_set(config('app.timezone', 'Africa/Accra'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(config('auth.session_name', 'daysitech_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

function api_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function api_input(): array
{
    $raw = file_get_contents('php://input');
    $json = $raw ? json_decode($raw, true) : null;
    return is_array($json) ? array_merge($_POST, $json) : $_POST;
}

function api_success(array $data = [], int $status = 200): never
{
    http_response_code($status);
    echo json_encode(['success' => true] + $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function api_error(string $message, int $status = 400, array $data = []): never
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message] + $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function api_require_method(string ...$methods): void
{
    if (!in_array(api_method(), $methods, true)) {
        header('Allow: ' . implode(', ', $methods));
        api_error('Method not allowed.', 405);
    }
}

function api_require_auth(): array
{
    $user = current_user();
    if (!$user) {
        api_error('Authentication required.', 401);
    }
    return $user;
}

function api_require_admin(): array
{
    $user = api_require_auth();
    if (!in_array($user['role'] ?? '', ['admin', 'staff'], true)) {
        api_error('Administrator access required.', 403);
    }
    return $user;
}

function api_limit(): int
{
    return max(1, min(100, (int) ($_GET['limit'] ?? 25)));
}

set_exception_handler(function (Throwable $exception): void {
    log_message('error', 'API: ' . $exception->getMessage() . ' @ ' . $exception->getFile() . ':' . $exception->getLine());
    api_error(config('app.debug') ? $exception->getMessage() : 'An internal server error occurred.', 500);
});
