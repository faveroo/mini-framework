<?php

namespace W0q\Request\Http;

use RuntimeException;
use W0q\Request\Core\Container;

class Router
{
    private array $routes = [];

    public function __construct(
        private Container $container,
    ) {}

    public function get(string $uri, mixed $action): Route
    {
        return $this->add(
            'GET', 
            $uri,
            $action
        );
    }

    public function post(
        string $uri,
        mixed $action
    ): Route {
        return $this->add(
            'POST',
            $uri,
            $action
        );
    }

    public function put(
        string $uri,
        mixed $action
    ): Route {
        return $this->add(
            'PUT',
            $uri,
            $action
        );
    }

    public function patch(
        string $uri,
        mixed $action
    ): Route {
        return $this->add(
            'PATCH',
            $uri,
            $action
        );
    }

    public function delete(
        string $uri,
        mixed $action
    ): Route {
        return $this->add(
            'DELETE',
            $uri,
            $action
        );
    }

    private function add(
        string $method,
        string $uri,
        mixed $action
    ): Route {
        $route = new Route(
            method: $method,
            uri: $uri,
            action: $action
        );

        $this->routes[] = $route;

        return $route;
    }
    public function dispatch(Request $request): mixed
    {
        foreach ($this->routes as $route) {
            if (! $route->matches(
                $request->method(),
                $request->path()
            )) {
                continue;
            }

            $parameters = $route->parameters(
                $request->path()
            );

            return $this->dispatchAction(
                $route->action(),
                $request,
                $parameters
            );
        }

        return $this->notFound();
    }

    private function dispatchAction(
        mixed $action,
        Request $request,
        array $parameters
    ): mixed {
        if (is_array($action)) {
            [$controller, $method] = $action;

            return $this->container->call(
                $controller,
                $method,
                $parameters
            );
        }

        if (is_callable($action)) {
            return $this->container->call(
                $action,
                null,
                $parameters
            );
        }

        throw new RuntimeException(
            'Invalid route action.'
        );
    }

    private function notFound(): array
    {
        http_response_code(404);

        return [
            'message' => 'Route not found'
        ];
    }
}
