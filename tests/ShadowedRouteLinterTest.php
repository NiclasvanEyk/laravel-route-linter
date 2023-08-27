<?php

use Illuminate\Support\Facades\Route;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\ShadowedRouteLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

/**
 * @param  string[]  $paths
 * @return Violation[]
 */
function computeViolations(array $paths): array
{
    foreach ($paths as $path) {
        Route::get($path, fn () => null);
    }

    $linter = new ShadowedRouteLinter();
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

it('does not produce false positives for different methods', function () {
    Route::post('/foo', function () {});
    $violations = computeViolations(['/foo']);
    expect($violations)->toBeEmpty();
});

it('does not produce false positives for different hosts', function () {
    $violations = computeViolations([
        'foo.com/foo',
        'bar.com/foo',
    ]);
    expect($violations)->toBeEmpty();
});
