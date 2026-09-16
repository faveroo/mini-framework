<?php

namespace W0q\Request\Http;

use W0q\Request\Core\Container;

class Router
{
    private array $routes = [];

    public function __construct(
        private Container $container,
    ) {}

    public function get(string $uri, callable|array $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->method();
        $path = $request->path();
        $action = $this->routes[$method][$path] ?? null;

        if ($action === null) {
            http_response_code(404);

            return [
                'message' => 'Route not found',
            ];
        }

        [$controller, $controllerMethod] = $action;

        return $this->container->call(
            $controller,
            $controllerMethod
        );
    }
}
