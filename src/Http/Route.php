<?php

namespace W0q\Request\Http;

class Route
{
    public function __construct(
        private string $method,
        private string $uri,
        private mixed $action,
        private ?string $name = null
    ) {}

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function action(): mixed
    {
        return $this->action;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function matches(
        string $method,
        string $path,
    ): bool {
        if ($this->method !== strtoupper($method)) {
            return false;
        }

        $pattern = $this->compileUri();

        return preg_match(
            $pattern,
            $path
        ) === 1;
    }

    public function parameters(
        string $path
    ): array {
        $pattern = $this->compileUri();

        preg_match(
            $pattern,
            $path,
            $matches
        );

        unset($matches[0]);

        return $matches;
    }

    public function compileUri(): string
    {
        $pattern = preg_replace(
            '/\{([^}]+)\}/',
            '(?P<$1>[^/]+)',
            $this->uri
        );

        return '#^' . $pattern . '$#';
    }
}