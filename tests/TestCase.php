<?php

namespace PhpTsRpc\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use PhpTsRpc\PhpTsRpcServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            PhpTsRpcServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('data.validation_strategy', 'always');

        config()->set('data.normalizers', [
            \Spatie\LaravelData\Normalizers\ModelNormalizer::class,
            \Spatie\LaravelData\Normalizers\ArrayableNormalizer::class,
            \Spatie\LaravelData\Normalizers\ObjectNormalizer::class,
            \Spatie\LaravelData\Normalizers\ArrayNormalizer::class,
            \Spatie\LaravelData\Normalizers\JsonNormalizer::class,
        ]);

        // config()->set('typescript-transformer', [
        //     // 'auto_discover_types' => [
        //     //     app_path()
        //     // ],

        //     // 'collectors' => [
        //     //     \Spatie\TypeScriptTransformer\Collectors\DefaultCollector::class,
        //     // ],

        //     'transformers' => [
        //         // \Spatie\LaravelTypeScriptTransformer\Transformers\SpatieStateTransformer::class,
        //         // \Spatie\TypeScriptTransformer\Transformers\SpatieEnumTransformer::class,
        //         \Spatie\TypeScriptTransformer\Transformers\DtoTransformer::class,
        //     ],

        //     // 'default_type_replacements' => [
        //     //     \DateTime::class => 'string',
        //     //     \DateTimeImmutable::class => 'string',
        //     //     \Carbon\CarbonImmutable::class => 'string',
        //     //     \Carbon\Carbon::class => 'string',
        //     // ],

        //     // 'output_file' => resource_path('types/generated.d.ts'),

        //     // 'writer' => \Spatie\TypeScriptTransformer\Writers\TypeDefinitionWriter::class,

        //     // 'formatter' => null,

        //     // 'transform_to_native_enums' => false,
        // ]);
    }
}
