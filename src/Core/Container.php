<?php

namespace W0q\Request\Core;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

class Container
{
    private array $bindings = [];

    public function bind(
        string $abstract,
        callable|string $concrete,
    ): void {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract): object
    {
        if (isset($this->bindings[$abstract])) {
            $concrete = $this->bindings[$abstract];

            if (is_callable($concrete)) {
                return $concrete($this);
            }

            $abstract = $concrete;
        }

        $reflection = new ReflectionClass($abstract);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $dependencies = [];

        foreach($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Não foi possível resolver {$parameter->getName()}"
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    public function call(
        mixed $target,
        ?string $method = null,
        array $parameters = []
    ): mixed {
        if (is_string($target)) {
            $instance = $this->make($target);

            $reflection = new ReflectionMethod(
                $instance,
                $method
            );
        } else {
            $instance = null;

            $reflection = new ReflectionFunction(
                $target
            );
        }

        $arguments = [];

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();

            if (array_key_exists($name, $parameters)) {
                $value = $parameters[$name];

                $arguments[] = $this->cast($parameter, $value);

                continue;
            }

            $type = $parameter->getType();

            if (
                $type === null ||
                $type->isBuiltin()
            ) {
                throw new RuntimeException(
                    "Não foi possível resolver {$name}"
                );
            }

            $arguments[] = $this->make(
                $type->getName()
            );
        }

        return $reflection->invokeArgs(
            $instance,
            $arguments
        );
    }

    public function cast(
        ReflectionParameter $parameter,
        mixed $value
    ): mixed {
        $type = $parameter->getType();

        if ($type === null) {
            return $value;
        }

        if (! $type->isBuiltin()) {
            return $value;
        }

        return match ($type->getName()) {
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => (bool) $value,
            'string' => (string) $value,
            'array' => (array) $value,
            default => $value,
        };
    }
}