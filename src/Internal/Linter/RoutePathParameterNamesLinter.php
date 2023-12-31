<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\ConfusingImplicitPathParameterBindings;
use ReflectionNamedType;
use ReflectionParameter;

use function enum_exists;

/**
 * Ensures that implicit route bindings that will be resolved from path
 * parameters have the same names as in the path.
 *
 * This prevents errors like this:
 * ```
 * Route::get('/articles/{slug}/comment/{id}', function (string $id, string $slug) {
 *   return ['slug' => $slug, 'id' => $id];
 * }
 *
 * get("/articles/why-static-analysis-rocks/comment/1")->body();
 * // => '{"slug": "1", "id": "why-static-analysis-rocks"}'
 * ```
 *
 * Route parameters [are bound in the order of their appearance in the path](https://laravel.com/docs/10.x/routing#required-parameters),
 * regardless of their name. This can lead to subtle errors as demonstrated above,
 * where the `slug` and `id` have misleading contents.
 *
 * This linter ensures, that the names _always_ match.
 */
final readonly class RoutePathParameterNamesLinter implements Linter
{
    /**
     * @param  list<RouteInformation>  $routes
     * @return list<Violation>
     */
    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($routes as $route) {
            $parameterNames = $this->injectionPoints($route->handler->getParameters());
            $pathParameterNames = $route->pathParameters;

            for ($index = 0; $index < count($parameterNames); $index++) {
                $nameInRouteDefinition = $pathParameterNames[$index] ?? null;
                $functionParameterName = $parameterNames[$index] ?? null;

                if ($nameInRouteDefinition !== $functionParameterName) {
                    $violations[] = new ConfusingImplicitPathParameterBindings(
                        route: $route,
                        expected: $pathParameterNames,
                        actual: $parameterNames,
                    );

                    // Only one violation per controller action. If one is wrong
                    // all others would lead to violations as well, but they
                    // would be mostly unnecessary noise.
                    break 2;
                }
            }
        }

        return $violations;
    }

    /**
     * @param  ReflectionParameter[]  $functionParameters
     * @return string[]
     */
    private function injectionPoints(array $functionParameters): array
    {
        $names = [];

        foreach ($functionParameters as $parameter) {
            $type = $parameter->getType();
            if ($type === null) {
                continue;
            }
            if (! ($type instanceof ReflectionNamedType)) {
                continue;
            }
            if (! $type->isBuiltin() && ! enum_exists($type->getName())) {
                continue;
            }

            $names[] = $parameter->getName();
        }

        return $names;
    }
}
