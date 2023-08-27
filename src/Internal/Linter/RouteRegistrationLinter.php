<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;

final readonly class RouteRegistrationLinter implements Linter
{
    /**
     * {@inheritDoc}
     */
    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($routes as $subject) {
            // TODO:
            foreach ($routes as $other) {
                if ($this->detectedThatRoutesClash($subject, $other)) {
                }
            }
        }

        return [];
    }

    private function detectedThatRoutesClash(
        RouteInformation $subject,
        RouteInformation $other,
    ): bool {
    }
}
