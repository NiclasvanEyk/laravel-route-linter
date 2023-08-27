<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
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
        public RoutePath $path,
        public array $pathParameters,
        public array $functionParameters,
    ) {
    }

    /**
     * @return self[]
     */
    public static function all(): array
    {
        return resolve(ResolveRouteInformation::class)();
    }
}
