<?php

namespace W0q\Request\Core;

use ReflectionClass;
use ReflectionMethod;
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
        string $class,
        string $method,
    ): mixed {
        $instance = $this->make($class);

        $reflection = new ReflectionMethod(
            $instance,
            $method
        );

        $dependencies = [];

        foreach ($reflection->getParameters() as $parameter) {
            $type = $parameter->getType();

            if ($type === null || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Não foi possível resolver {$parameter->getName()}"
                );
            }

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->invokeArgs(
            $instance,
            $dependencies
        );
    }
}