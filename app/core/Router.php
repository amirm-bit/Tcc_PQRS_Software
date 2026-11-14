<?php
// app/core/Router.php - Enrutador simple con soporte de parámetros y middleware

class Router {
    private array $routes = [];
    private string $basePath = '';

    public function __construct(string $basePath = '') {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $path, array $handler, array $middleware = []): void {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array $handler, array $middleware = []): void {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array $handler, array $middleware): void {
        $path = '/' . trim($path, '/');
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        $this->routes[] = [
            'method'     => $method,
            'path'       => $path,
            'pattern'    => '#^' . $pattern . '$#',
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $path = $this->extractPath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            if (preg_match($route['pattern'], $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    $result = Middleware::run($mw);
                    if (($result[1] ?? true) === false) {
                        return;
                    }
                }

                [$controllerClass, $action] = $route['handler'];
                if (!class_exists($controllerClass)) {
                    Response::error(500, 'Controlador no encontrado: ' . $controllerClass);
                    return;
                }
                $controller = new $controllerClass();
                if (!method_exists($controller, $action)) {
                    Response::error(500, 'Acción no encontrada en el controlador.');
                    return;
                }
                call_user_func_array([$controller, $action], array_values($params));
                return;
            }
        }

        $this->notFound($path);
    }

    private function extractPath(string $uri): string {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        if ($this->basePath !== '' && str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }
        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    private function notFound(string $path): void {
        http_response_code(404);
        $app = require __DIR__ . '/../../config/app.php';
        View::render('errors/404', ['path' => $path], 'public', ['title' => 'Página no encontrada']);
    }
}
