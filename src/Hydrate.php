<?php

declare(strict_types=1);

namespace Hermes;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionEnum;
use ReflectionType;
use ReflectionUnionType;

class Hydrate
{
    public static function hydrate(array $data, string $class): object
    {
        $ref = new ReflectionClass($class);
        $ctor = $ref->getConstructor();

        if (! $ctor) {
            throw new InvalidArgumentException("Class $class has no constructor");
        }

        $args = [];
        foreach ($ctor->getParameters() as $p) {
            $name = $p->getName();
            $type = $p->getType();

            if (! $type) {
                throw new InvalidArgumentException("Missing type for parameter: $name");
            }

            if (! array_key_exists($name, $data)) {
                if ($p->isDefaultValueAvailable()) {
                    $args[] = $p->getDefaultValue();

                    continue;
                }
                if ($type->allowsNull()) {
                    $args[] = null;

                    continue;
                }
                throw new InvalidArgumentException("Missing required field: $name");
            }

            try {
                $args[] = self::coerce($data[$name], $type);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException("Error initializing $class::\$$name ".$e->getMessage(), previous: $e);
            }
        }

        return $ref->newInstanceArgs($args);
    }

    public static function coerce(mixed $val, ReflectionType $type): mixed
    {
        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $t) {
                try {
                    return self::coerce($val, $t);
                } catch (InvalidArgumentException) {
                    // try next
                }
            }

            throw new InvalidArgumentException('Value does not match any union type');
        }

        /** @var ReflectionNamedType $type */
        $name = $type->getName();

        if ($type->isBuiltin()) {
            if ($val === null && $type->allowsNull()) {
                return null;
            }

            if ($name === 'array' && array_is_list($val)) {
                // TODO: safely init array and check for type hints
                return $val;
            }

            return match ($name) {
                'int' => is_int($val) ? $val : throw new InvalidArgumentException('Expected int'),
                'float' => is_float($val) ? $val : throw new InvalidArgumentException('Expected float'),
                'bool' => is_bool($val) ? $val : throw new InvalidArgumentException('Expected bool'),
                'string' => is_string($val) ? $val : throw new InvalidArgumentException('Expected string'),
                default => throw new InvalidArgumentException('Unsupported type: '.$name),
            };
        }

        if (enum_exists($name)) {
            $re = new ReflectionEnum($name);

            if ($re->isBacked()) {
                $type = $re->getBackingType()->getName();
                $val = match ($type) {
                    'string' => is_string($val) ? $val : throw new InvalidArgumentException('Expected string'),
                    'int' => is_int($val) ? $val : throw new InvalidArgumentException('Expected int'),
                    default => throw new InvalidArgumentException('Unsupported backing type: '.$type),
                };

                return $name::tryFrom($val) ?? throw new InvalidArgumentException('Invalid enum case');
            }

            throw new InvalidArgumentException('Unit enums are not supported');
        }

        if (is_array($val)) {
            return self::hydrate($val, $name);
        }

        throw new InvalidArgumentException("Cannot hydrate $name from non-array");
    }
}
