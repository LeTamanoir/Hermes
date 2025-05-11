<?php

namespace PhpTsRpc\Commands;

use Illuminate\Console\Command;
use PhpTsRpc\Facades\Router;
use ReflectionClass;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;

class GenerateCommand extends Command
{
    public $signature = 'php-ts-rpc:generate';

    public $description = 'Generate the TypeScript client.';

    public function handle(): int
    {
        $this->comment('Generating the TypeScript client');

        $reflection = new ReflectionClass(Router::getFacadeRoot());

        $types = [];

        $T = "export type Datas = {\n";

        $R = "export type Routes = {\n";

        /**
         * @var array<string, array<string, array{callback: \Closure, parameters: list<class-string<\Spatie\LaravelData\Data>>}>>
         */
        $all_routes = $reflection->getStaticPropertyValue('routes');

        $config = app(TypeScriptTransformerConfig::class);
        $config->transformers([
            \Spatie\TypeScriptTransformer\Transformers\DtoTransformer::class,
        ]);

        foreach ($all_routes as $method => $routes) {

            $R .= sprintf("    '%s': {\n", $method);

            foreach ($routes as $path => $route) {

                $R .= sprintf("        '%s': {\n", $path);
                $R .= sprintf("            params: {\n", $path);

                foreach ($route['parameters'] as $index => $parameter) {
                    foreach ($config->getCollectors() as $collector) {
                        $transformedType = $collector->getTransformedType(new ReflectionClass($parameter));

                        $types[$transformedType->getTypeScriptName()] = $transformedType->transformed;

                        $R .= sprintf("                '%s': Datas['%s']", $parameter, $transformedType->getTypeScriptName());

                        if ($index !== count($route['parameters']) - 1) {
                            $R .= sprintf(",\n");
                        } else {
                            $R .= sprintf("\n");
                        }
                    }
                }

                $R .= sprintf("            },\n");

                $R .= sprintf("        },\n");
            }
            $R .= sprintf("    }\n");
        }

        $R .= sprintf("}\n");

        foreach ($types as $type => $transformed) {
            $T .= sprintf("    '%s': %s;\n", $type, $transformed);
        }

        $T .= sprintf("}\n");

        file_put_contents('./resources/types/routes.d.ts', "$T\n$R");

        $this->comment('All done');

        return self::SUCCESS;
    }
}
