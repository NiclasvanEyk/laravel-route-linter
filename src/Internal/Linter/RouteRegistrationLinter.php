<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Constant;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Variable;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

use function array_shift;
use function array_slice;
use function count;
use function min;

final readonly class RouteRegistrationLinter implements Linter
{
    /**
     * {@inheritDoc}
     */
    public function lint(array $routes): array
    {
        $violations = [];
        if (count($routes) < 2) {
            return $violations;
        }

        /** @var list<RouteInformation> $registered */
        $registered = [array_shift($routes)];

        while ($new = array_shift($routes)) {
            foreach ($registered as $existing) {
                if (self::doesNewSegmentsClash($new->path->segments, $existing->path->segments)) {
                    $violations[] = new Violation(
                        "The route {$new->path->pattern} clashes with an existing route definition ({$existing->path->pattern})",
                        Confidence::Probably,
                    );
                    break;
                }

                $registered[] = $new;
            }
        }

        return $violations;
    }

    /**
     * @param  list<Constant|Variable>  $newTokens
     * @param  list<Constant|Variable>  $existingTokens
     */
    public static function doesNewSegmentsClash(
        array $newTokens,
        array $existingTokens,
    ): bool {
        $segmentsToCompare = min(
            count($newTokens),
            count($existingTokens),
        );
        $newTokens = array_slice($newTokens, 0, $segmentsToCompare);
        $existingTokens = array_slice($existingTokens, 0, $segmentsToCompare);

        for ($index = 0; $index < $segmentsToCompare; $index++) {
            $new = $newTokens[$index];
            $existing = $existingTokens[$index];

            // We know the routes won't clash, if a different constant prefix is
            // found...
            if ($existing instanceof Constant && $new instanceof Constant) {
                if ($new->text !== $existing->text) {
                    return false;
                }
            }

            // ...or if the existing is constant and the one is a variable.
            if ($existing instanceof Constant && $new instanceof Variable) {
                return false;
            }

            // The other two cases might lead to clashes. Further complex logic
            // could be implemented here to check for overlapping regexes, but
            // this would DRASTICALLY improve the complexity, so for now we put
            // the burden on the user to annotate false positives as ignored.
        }

        return true;
    }
}
