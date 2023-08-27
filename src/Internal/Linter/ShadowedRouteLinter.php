<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Constant;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Variable;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\RouteShadowed;
use function array_intersect;
use function array_shift;
use function count;

/**
 * Detects when a route is "shadowed" by another route.
 *
 * A common mistake is to register your routes like this:
 * ```
 * Route::get('/articles/{article}', fn (Article $article) => $article);
 * Route::get('/articles/new', fn () => view('articles.new'));
 * ```
 * The second route _can not be reached_, since the URL `/articles/new` is
 * already matching the first route.
 */
final readonly class ShadowedRouteLinter implements Linter
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
                // If the routes do not share http methods, they can't clash.
                // We can safely register `GET /foo` even when `POST /foo`
                // already exists.
                if (count(array_intersect($new->methods, $existing->methods)) === 0) {
                    continue;
                }

                if (self::doesNewSegmentsClash($new->path->segments, $existing->path->segments)) {
                    $violations[] = new RouteShadowed($new, $existing);
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
        while (true) {
            $new = array_shift($newTokens);
            $existing = array_shift($existingTokens);
            if ($new === null && $existing === null) {
                break;
            }

            // We know the routes won't clash, if a different constant prefix is
            // found:
            // - GET `/articles/list` exists
            // - GET `/articles/new` is registered
            if ($existing instanceof Constant && $new instanceof Constant) {
                if ($new->text !== $existing->text) {
                    return false;
                }
            }

            // ...or if the existing is constant and the one is a variable:
            // - GET `/articles/new` exists
            // - GET `/articles/{slug}` is registered
            if ($existing instanceof Constant && $new instanceof Variable) {
                return false;
            }

            // ...or if we could not find any clashing segments and there are
            // no more tokens in the existing route to compare against:
            // - GET `/articles` exists
            // - GET `/articles/{slug} is registered
            if (count($existingTokens) === 0 && count($newTokens) > 0) {
                return false;
            }
        }

        // If we cannot prove, that the route does not clash, we assume it does.
        // This might lead to false positive for regex parameters (e.g. there)
        // could be two controllers for `/articles/{slug}` and `/articles/{id}`
        // that use regexes to route string parameters to the first and all
        // numeric ones to the second. However, this case is (I assume) not that
        // common, so we'll make the user explicitly ignore these cases.
        return true;
    }
}
