<?php

namespace W0q\Request\Http;

class Router
{
    private array $routes = [];

    public function get(string $uri, callable $action): void
    {
        $this->routes['GET'][$uri] = $action;
    }

    public function dispatch(Request $request): mixed
    {
        $method = $request->method();
        $uri = parse_url($request->uri(), PHP_URL_PATH);

        $action = $this->routes[$method][$uri] ?? null;

        if ($action === null) {
            http_response_code(404);

            return [
                'message' => 'Route not found',
            ];
        }

        return $action($request);
    }
}
