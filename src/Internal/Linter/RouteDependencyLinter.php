<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

use function in_array;

final readonly class RouteDependencyLinter implements Linter
{
    /**
     * @param  list<RouteInformation>  $routes
     * @return list<Violation>
     */
    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($routes as $route) {
            foreach ($route->functionParameters as $dependency) {
                $type = $dependency->getType();
                if (in_array($dependency->name, $route->pathParameters)) {
                    // TODO: Check type
                    continue;
                }

            }
        }

        return $violations;
    }
}
