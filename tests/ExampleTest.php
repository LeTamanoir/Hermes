<?php

use Hermes\Rpc;

it('can generate types', function () {

    enum Status: int
    {
        case TODO = 0;
        case DONE = 1;
    }

    class Child
    {
        public function __construct(
            public string $title,
        ) {}
    }

    class HelloInput
    {
        public function __construct(
            public Child $child,
            public string $name,
            public Status $status,
            public array $tags,
        ) {}
    }

    class HelloResponse
    {
        public function __construct(
            public string $message,
            public array $tags,
        ) {}
    }

    $rpc = new Rpc()
        ->query(
            'Hello',
            function (HelloInput $input): HelloResponse {
                dump($input);

                return new HelloResponse(
                    message: "Hello, {$input->name}!",
                    tags: $input->tags,
                );
            }
        );

    $result = $rpc->handle('query:Hello', [
        'child' => [
            'title' => 'toto',
        ],
        'name' => 'John',
        'status' => 1,
        'tags' => ['toto', 'titi'],
    ]);

    dd($result);

});
