<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RouteRegistrationLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;

it('detects simple shadowed routes', function (array $paths) {
    array_map(fn (string $path) => Route::get($path, fn () => null), $paths);

    $linter = new RouteRegistrationLinter();
    $routes = RouteInformation::all();
    $violations = $linter->lint($routes);

    expect($violations)->not()->toBeEmpty();
})->with([
    [[
        '/articles/{article}/comments/{comment}',
        '/articles/{article}/comments/new',
    ]],
    [[
        '/constant/prefix/{variable}',
        '/constant/prefix/constant',
    ]],
]);
