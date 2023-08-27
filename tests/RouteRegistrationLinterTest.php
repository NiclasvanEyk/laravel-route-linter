<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RouteRegistrationLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

/**
 * @param string[] $paths
 * @return Violation[]
 */
function computeViolations(array $paths): array
{
    array_map(fn (string $path) => Route::get($path, fn () => null), $paths);

    $linter = new RouteRegistrationLinter();
    $routes = RouteInformation::all();
    $violations = $linter->lint($routes);

    return $violations;
}

it('detects simple shadowed routes', function (array $paths) {
    $violations = computeViolations($paths);
    expect($violations)->not()->toBeEmpty();
})->with([
    [[
        // This one is mentioned in the doc-block
        '/articles/{article}',
        '/articles/new',
    ]],
    [[
        '/articles/{article}/comments/{comment}',
        '/articles/{article}/comments/new',
    ]],
]);

it('does not produce false positives', function (array $paths) {
    $violations = computeViolations($paths);
    expect($violations)->toBeEmpty();
})->with([
    [[
        '/articles',
        '/articles/all',
        '/articles/{article}',
    ]],
]);
