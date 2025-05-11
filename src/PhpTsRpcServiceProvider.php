<?php

namespace LeTamanoir\PhpTsRpc;

use LeTamanoir\PhpTsRpc\Commands\PhpTsRpcCommand;
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
            ->hasViews()
            ->hasMigration('create_php_ts_rpc_table')
            ->hasCommand(PhpTsRpcCommand::class);
    }
}
