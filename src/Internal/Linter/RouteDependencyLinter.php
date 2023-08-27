<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Linter;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;
use ReflectionNamedType;
use ReflectionParameter;
use function enum_exists;
use function in_array;

readonly final class RouteDependencyLinter implements Linter
{
    /**
     * @param list<RouteInformation> $routes
     * @return list<Violation>
     */
    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($routes as $route) {
            $parameterNames = $this->injectionPoints($route->functionParameters);
            $pathParameterNames = $route->pathParameters;

            for ($index = 0; $index < count($parameterNames); $index++) {
                $nameInRouteDefinition = $pathParameterNames[$index] ?? null;
                $functionParameterName = $parameterNames[$index] ?? null;

                if ($nameInRouteDefinition !== $functionParameterName) {
                    $expected = implode(', ', $parameterNames);
                    $actual = implode(', ', $pathParameterNames);

                    $violations[] = new Violation(
                        "The function parameters of handler of '{$route->path->pattern}' seem misleading. Their order in the path is [$expected], but in the controller the order is [$actual]",
                        Confidence::Definite,
                    );

                    // Only one violation per controller action. If one is wrong
                    // all others would lead to violations as well, but they
                    // would be mostly unnecessary noise.
                    break;
                }
            }
        }

        return $violations;
    }

    /**
     * @param ReflectionParameter[] $functionParameters
     * @return string[]
     */
    private function injectionPoints(array $functionParameters): array
    {
        $names = [];

        foreach ($functionParameters as $parameter) {
            $type = $parameter->getType();
            if ($type === null) continue;
            if (!($type instanceof ReflectionNamedType)) continue;
            if (!$type->isBuiltin() && !enum_exists($type->getName())) continue;

            $names[] = $parameter->getName();
        }

        return $names;
    }
}
