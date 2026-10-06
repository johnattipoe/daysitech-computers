<?php
/**
 * Daysitech Computers — Front Controller
 * ---------------------------------------
 * Point your web server's document root at this /public directory.
 * (If your host only lets you use the project root, the root-level
 * index.php forwards requests here automatically.)
 */

declare(strict_types=1);

define('APP_START', microtime(true));

$root = dirname(__DIR__);

// ---------------------------------------------------------------
// 1) Autoloading — prefer Composer, fall back to a tiny PSR-4 loader
//    so the app still runs even without `composer install`.
// ---------------------------------------------------------------
if (file_exists($root . '/vendor/autoload.php')) {
    require $root . '/vendor/autoload.php';
} else {
    spl_autoload_register(function ($class) use ($root) {
        $prefixes = [
            'App\\Controllers\\' => '/app/Controllers/',
            'App\\Middleware\\'  => '/app/Middleware/',
        ];
        foreach ($prefixes as $prefix => $dir) {
            if (str_starts_with($class, $prefix)) {
                $relative = substr($class, strlen($prefix));
                $file = $root . $dir . str_replace('\\', '/', $relative) . '/' . basename($relative) . '.php';
                if (file_exists($file)) require $file;
                return;
            }
        }
    });
}

spl_autoload_register(function ($class) use ($root) {
    foreach (['App\\Controllers', 'App\\Helpers', 'App\\Middleware', 'App\\Models', 'App\\Services'] as $namespace) {
        $prefix = $namespace . '\\';
        if (str_starts_with($class, $prefix)) {
            $name = substr($class, strlen($prefix));
            $file = $root . '/app/' . substr($namespace, 4) . '/' . $name . '/' . basename($name) . '.php';
            if (file_exists($file)) require $file;
            return;
        }
    }
});

// ---------------------------------------------------------------
// 2) Environment variables (.env) — Composer's vlucas/phpdotenv if
//    available, otherwise a minimal built-in parser.
// ---------------------------------------------------------------
if (class_exists(\Dotenv\Dotenv::class)) {
    \Dotenv\Dotenv::createImmutable($root)->safeLoad();
} elseif (file_exists($root . '/.env')) {
    foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// ---------------------------------------------------------------
// 3) Helpers (functions.php files aren't PSR-4 classes, so require
//    them explicitly — matches composer.json's "files" autoload).
// ---------------------------------------------------------------
require_once $root . '/app/Helpers/functions/functions.php';
require_once $root . '/app/Helpers/security/security.php';
require_once $root . '/app/Helpers/validation/validation.php';
require_once $root . '/app/Helpers/format/format.php';
require resolve_php_file($root . '/config', 'constants');

// ---------------------------------------------------------------
// 4) Runtime setup
// ---------------------------------------------------------------
date_default_timezone_set(config('app.timezone', 'Africa/Accra'));

if (config('app.debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
}

session_name(config('auth.session_name', 'daysitech_session'));
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

$render500Page = static function (): void {
    $errorView = resolve_php_file(base_path('views/pages'), '500');
    if (is_file($errorView)) {
        require $errorView;
        return;
    }

    echo '<!doctype html><html lang="en"><title>500 — Server Error</title><h1>500 — Something Went Wrong</h1></html>';
};

set_exception_handler(function (Throwable $e) use ($render500Page): void {
    log_message('error', $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);

    if (config('app.debug')) {
        echo '<pre style="background:#111;color:#f66;padding:1.5rem;">' . e($e->getMessage()) . "\n\n" . e($e->getTraceAsString()) . '</pre>';
        return;
    }

    $render500Page();
});

register_shutdown_function(static function () use ($render500Page): void {
    $error = error_get_last();
    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    if (!$error || !in_array($error['type'], $fatalTypes, true)) return;

    log_message('error', $error['message'] . ' @ ' . $error['file'] . ':' . $error['line']);
    if (!config('app.debug')) {
        if (!headers_sent()) http_response_code(500);
        $render500Page();
    }
});

// ---------------------------------------------------------------
// 5) Router + middleware map
// ---------------------------------------------------------------
$router = new \App\Helpers\Router();

$router->setMiddlewareMap([
    'auth'  => fn () => \App\Middleware\AuthMiddleware::handle(),
    'admin' => fn () => \App\Middleware\AdminMiddleware::handle(),
    'role'  => fn (array $roles) => \App\Middleware\RoleMiddleware::handle($roles),
]);

require resolve_php_file($root . '/routes', 'web');
require resolve_php_file($root . '/routes', 'api');
require resolve_php_file($root . '/routes', 'admin');

// ---------------------------------------------------------------
// 6) Dispatch
// ---------------------------------------------------------------
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'] ?? '/');
