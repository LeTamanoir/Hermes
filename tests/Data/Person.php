<?php

namespace PhpTsRpc\Tests\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class Person extends Data
{
    public function __construct(
        public string $name,

        public int $age,
    ) {}
}
