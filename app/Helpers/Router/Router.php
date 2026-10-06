<?php

namespace App\Helpers;

/**
 * Router — a small, dependency-free router.
 *
 *   $router->get('/products/{slug}', [ProductController::class, 'details']);
 *   $router->post('/cart/add', [CartController::class, 'add'], ['auth']);
 *
 * Supports {param} segments, a group() helper for shared prefix/middleware,
 * and simple named middleware resolved via $middlewareMap.
 */
class Router
{
    protected array $routes = [];
    protected string $groupPrefix = '';
    protected array $groupMiddleware = [];
    protected array $middlewareMap = [];

    public function setMiddlewareMap(array $map): void
    {
        $this->middlewareMap = $map;
    }

    public function get(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    public function any(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
        $this->add('POST', $uri, $action, $middleware);
    }

    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix .= $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    protected function add(string $method, string $uri, callable|array $action, array $middleware): void
    {
        $fullUri = rtrim($this->groupPrefix . $uri, '/');
        $fullUri = $fullUri === '' ? '/' : $fullUri;

        $this->routes[] = [
            'method'     => $method,
            'uri'        => $fullUri,
            'action'     => $action,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim(parse_url($uri, PHP_URL_PATH), '/');
        $uri = $uri === '' ? '/' : $uri;

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                foreach ($route['middleware'] as $mw) {
                    $this->resolveMiddleware($mw);
                }

                $this->callAction($route['action'], $matches);
                return;
            }
        }

        http_response_code(404);
        if (file_exists(resolve_php_file(base_path('views/pages'), '404'))) {
            view('pages.404', ['title' => 'Page Not Found']);
        } else {
            echo '404 Not Found';
        }
    }

    protected function resolveMiddleware(string $name): void
    {
        $param = null;
        if (str_contains($name, ':')) {
            [$name, $param] = explode(':', $name, 2);
        }

        $handler = $this->middlewareMap[$name] ?? null;
        if (!$handler) return;

        $param !== null ? $handler(explode(',', $param)) : $handler();
    }

    protected function callAction(callable|array $action, array $params): void
    {
        if (is_callable($action) && !is_array($action)) {
            call_user_func_array($action, $params);
            return;
        }

        [$class, $method] = $action;
        $instance = new $class();
        call_user_func_array([$instance, $method], $params);
    }
}
