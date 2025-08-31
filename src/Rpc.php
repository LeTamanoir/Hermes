<?php

namespace Hermes;

use InvalidArgumentException;
use ReflectionFunction;
use ReflectionNamedType;

class Rpc
{
    public array $procedures = [];

    public function query(string $name, callable $handler): self
    {
        $p = new ReflectionFunction($handler)->getParameters();

        if (count($p) !== 1) {
            throw new InvalidArgumentException('Handler must have exactly one parameter');
        }

        $type = $p[0]->getType();

        if (! ($type instanceof ReflectionNamedType)) {
            throw new InvalidArgumentException('Handler must have exactly one parameter with one named type');
        }

        if ($type->isBuiltin() || ! class_exists($type->getName())) {
            throw new InvalidArgumentException('Handler must have a DTO as its only parameter');
        }

        $inputClass = $type->getName();

        $this->procedures["query:$name"] = function (array $input) use ($inputClass, $handler) {
            return $handler(Hydrate::hydrate($input, $inputClass));
        };

        return $this;
    }

    public function handle(string $method, array $input): mixed
    {
        return $this->procedures[$method]($input);
    }
}
