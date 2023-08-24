<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RouteRegistrationLinter;

it('detects simple shadowed routes', function () {
    Route::get('/constant/prefix/{variable}', function () {});
    Route::get('/constant/prefix/constant', function () {});

    $router = resolve('router');
    $routes = (new ResolveRouteInformation)($router);
    $violations = (new RouteRegistrationLinter)->lint($routes);

    expect($violations)->not()->toBeEmpty();
});
