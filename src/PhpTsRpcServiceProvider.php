<?php

namespace PhpTsRpc;

use PhpTsRpc\Commands\GenerateCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PhpTsRpcServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('php-ts-rpc')
            ->hasConfigFile()
            ->hasCommand(GenerateCommand::class);
    }
}
