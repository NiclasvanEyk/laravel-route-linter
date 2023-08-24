<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

final readonly class RouteRegistrationLinter implements Linter
{
    /**
     * {@inheritDoc}
     */
    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($routes as $route) {
            // TODO:
        }

        return [];
    }
}
