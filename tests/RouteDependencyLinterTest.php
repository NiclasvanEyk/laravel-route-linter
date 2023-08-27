<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RouteDependencyLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;

it('detects when scalar route dependencies have matching names, but a different order', function () {
    Route::get('/articles/{slug}/comment/{id}', fn (string $id, string $slug) => [
        'slug' => $slug,
        'id' => $id,
    ]);

    $routes = RouteInformation::all();
    $violations = (new RouteDependencyLinter)->lint($routes);

    expect($violations)->not()->toBeEmpty();
});
