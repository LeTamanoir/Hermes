<?php

namespace LeTamanoir\PhpTsRpc\Commands;

use Illuminate\Console\Command;

class PhpTsRpcCommand extends Command
{
    public $signature = 'php-ts-rpc';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
