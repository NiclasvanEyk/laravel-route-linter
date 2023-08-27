<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RouteDependencyLinter;

it('detects when scalar route dependencies have matching names, but a different order', function () {
    Route::get('/articles/{slug}/comment/{id}', fn (string $id, string $slug) => [
        'slug' => $slug,
        'id' => $id,
    ]);

    $router = resolve('router');
    $routes = (new ResolveRouteInformation)($router);
    $violations = (new RouteDependencyLinter)->lint($routes);

    expect($violations)->not()->toBeEmpty();
});
