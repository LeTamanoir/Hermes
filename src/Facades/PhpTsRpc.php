<?php

namespace LeTamanoir\PhpTsRpc\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LeTamanoir\PhpTsRpc\PhpTsRpc
 */
class PhpTsRpc extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LeTamanoir\PhpTsRpc\PhpTsRpc::class;
    }
}
