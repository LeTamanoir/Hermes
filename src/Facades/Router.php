<?php

namespace PhpTsRpc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \PhpTsRpc\Facades\Router make()
 * @method static \PhpTsRpc\Facades\Router get(string $path, \Closure $callback)
 */
class Router extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \PhpTsRpc\Router::class;
    }
}
