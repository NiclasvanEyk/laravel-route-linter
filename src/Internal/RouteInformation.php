<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

use ReflectionParameter;

/**
 * @internal
 */
final readonly class RouteInformation
{
    /**
     * @param  list<string>  $pathParameters
     * @param  list<ReflectionParameter>  $functionParameters
     */
    public function __construct(
        public string $pattern,
        public array $pathParameters = [],
        public array $functionParameters = [],
    ) {
    }
}
