<?php

namespace W0q\Request\Http;

class Request
{
    public function __construct(
        private string $method,
        private string $uri,
        private array $query,
        private array $headers,
    ) {}

    public static function capture(): static
    {
        return new static(
            method: $_SERVER['REQUEST_METHOD'] ?? 'GET',
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            query: $_GET,
            headers: getallheaders(),
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function query(): array
    {
        return $this->query;
    }

    public function path(): string
    {
        return parse_url(
            $this->uri,
            PHP_URL_PATH
        );
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function all(): array
    {
        return [
            'method' => $this->method(),
            'uri' => $this->uri(),
            'query' => $this->query(),
            'headers' => $this->headers(),
        ];
    }
}