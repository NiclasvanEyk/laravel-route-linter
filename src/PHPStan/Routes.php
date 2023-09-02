<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;

/**
 * Container class collecting all routes registered by the application.
 */
class Routes
{
    /**
     * @param list<RouteInformation> $all
     */
    public function __construct(public readonly array $all)
    {
    }
}
