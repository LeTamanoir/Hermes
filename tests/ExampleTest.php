<?php

use Illuminate\Http\Request;
use PhpTsRpc\Facades\Router as FacadesRouter;
use PhpTsRpc\Router;
use PhpTsRpc\Tests\Data\Person;

use function Pest\Laravel\artisan;

it('can create a router', function () {

    $router = (new Router)
        ->get('/', function (Person $param): string {
            return 'Hello World';
        });

    $request = Request::create('/', 'GET', [['name' => 'John', 'age' => 30]]);

    $response = $router->handle($request);

    expect($response->getContent())->toBe('"Hello World"');

});

it('can generate the TypeScript client', function () {

    FacadesRouter::make()
        ->get('/', function (Person $param): string {
            return 'Hello World';
        })
        ->get('/toto', function (Person $a, Person $b): Person {
            return new Person($a->name.' '.$b->name, $a->age + $b->age);
        });

    artisan('php-ts-rpc:generate');

});
