<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

interface Linter
{
    /**
     * @param  list<RouteInformation>  $routes
     * @return list<Violation>
     */
    public function lint(array $routes): array;
}
