<?php

use NiclasVanEyk\LaravelRouteLinter\PHPStan\UrlGenerationPathParameters;

it('works', function (array $names, array $expectedResult) {
    $params = new UrlGenerationPathParameters($names);
    $result = $params->matchedTo(['first', 'second', 'third']);

    expect($result)->toBe($expectedResult);
})->with([
    'all present and named' => [
        ['first', 'second', 'third'],
        ['first', 'second', 'third'],
    ],
    'some present and named' => [
        ['first', 'third'],
        ['first', 'third'],
    ],
    'all present and numeric' => [
        [null, null, null],
        ['first', 'second', 'third'],
    ],
    'some present and numeric' => [
        [null, null],
        ['first', 'second'],
    ],
    'all present and cursed' => [
        ['third', null, 'second'],
        ['third', 'second', 'first'],
    ],
    'some present and cursed' => [
        ['third', null],
        ['third', 'first'],
    ],
]);
