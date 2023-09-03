<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
use ReflectionFunction;
use ReflectionMethod;
use Stringable;

use function implode;

/**
 * @internal
 */
final readonly class RouteInformation implements Stringable
{
    /**
     * @param  list<string>  $methods
     * @param  list<string>  $pathParameters
     */
    public function __construct(
        public array $methods,
        public ?string $name,
        public RoutePath $path,
        public array $pathParameters,
        public ReflectionFunction|ReflectionMethod $handler,
    ) {
    }

    /**
     * @return self[]
     */
    public static function all(): array
    {
        return resolve(ResolveRouteInformation::class)();
    }

    public function __toString(): string
    {
        $methods = implode('|', $this->methods);

        return "$methods {$this->path->pattern}";
    }
}
