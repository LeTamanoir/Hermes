<?php

declare(strict_types=1);

namespace PhpTsRpc;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;

class Router
{
    /**
     * @var array<string, array<string, array{callback: \Closure, parameters: list<class-string<\Spatie\LaravelData\Data>>}>>
     */
    protected static array $routes = [];

    protected static function addRoute(string $method, string $path, \Closure $callback): void
    {
        $reflection = new ReflectionFunction($callback);

        $parameters = [];

        foreach ($reflection->getParameters() as $parameter) {
            if (! $parameter->hasType()) {
                throw new InvalidArgumentException(sprintf(
                    'Parameter [%s] has no type. (%s:%d)',
                    $parameter->getName(), $reflection->getFileName(), $reflection->getStartLine()
                ));
            }

            $type = $parameter->getType();

            if ($type->allowsNull()) {
                throw new InvalidArgumentException(sprintf(
                    'Parameter [%s] type [%s] can not be nullable. (%s:%d)',
                    $parameter->getName(), $type->getName(), $reflection->getFileName(), $reflection->getStartLine()
                ));
            }

            if ($type->isBuiltin()) {
                throw new InvalidArgumentException(sprintf(
                    'Parameter [%s] type [%s] is not a class. (%s:%d)',
                    $parameter->getName(), $type->getName(), $reflection->getFileName(), $reflection->getStartLine()
                ));
            }

            $param_reflection = new ReflectionClass($type->getName());

            if (! $param_reflection->isSubclassOf(\Spatie\LaravelData\Data::class)) {
                throw new InvalidArgumentException(sprintf(
                    'Parameter [%s] type [%s] is not a subclass of [%s]. (%s:%d)',
                    $parameter->getName(), $type->getName(), \Spatie\LaravelData\Data::class, $reflection->getFileName(), $reflection->getStartLine()
                ));
            }

            $parameters[] = $param_reflection->getName();
        }

        static::$routes[$method][$path] = [
            'callback' => $callback,
            'parameters' => $parameters,
        ];
    }

    /**
     * @return array{callback: \Closure, parameters: list<class-string<\Spatie\LaravelData\Data>>}
     */
    protected static function getRoute(string $method, string $path): ?array
    {
        return static::$routes[$method][$path] ?? null;
    }

    public static function make(): self
    {
        return new self;
    }

    public static function get(string $path, \Closure $callback): self
    {
        static::addRoute('GET', $path, $callback);

        return new self;
    }

    public static function handle(Request $request): JsonResponse
    {
        $method = $request->getMethod();
        $path = $request->path();

        $route = static::getRoute($method, $path);

        if (! $route) {
            throw new \Exception('Route not found.');
        }

        $data = $request->all();

        if (! $data || ! is_array($data) || ! array_is_list($data)) {
            throw new \Exception('Invalid JSON.');
        }

        $data = Arr::isList($data) ? $data : [$data];

        if (count($data) !== count($route['parameters'])) {
            throw new \Exception('Invalid number of parameters.');
        }

        $parameters = [];

        foreach ($route['parameters'] as $index => $parameter) {
            $parameters[] = $parameter::validateAndCreate($data[$index]);
        }

        return response()->json($route['callback'](...$parameters), 200);
    }
}
