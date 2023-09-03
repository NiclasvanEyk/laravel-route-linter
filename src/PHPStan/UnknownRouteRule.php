<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\Reflection;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use function in_array;
use function is_array;
use function levenshtein;
use function usort;

/**
 * Validates that named routes do exist and that they get the correct parameters
 * when generating URLs.
 */
class UnknownRouteRule implements Rule
{
    /**
     * @var array<string,list<string>> Route names as keys and their path
     * parameters as values. Routes without names are not included.
     */
    public array $routes = [];

    /**
     * @param Routes|list<string> $routes
     */
    public function __construct(
        Routes|array $routes,
        private RoutingFunctions $routingFunctions,
    ) {
        $this->routes = is_array($routes)
            ? $routes
            : $this->getRouteNames($routes);
    }

    public function getNodeType(): string
    {
        return Node\Expr\CallLike::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node instanceof Node\Expr\CallLike)) return [];
        if (count($this->routes) === 0) return [];

        $argumentNode = $this->routingFunctions->getRouteNameArgument(
            $node,
            $scope,
        );

        if ($argumentNode === null) return [];

        $value = Reflection::constantStringValueOf($argumentNode);
        if ($value === null) return [];
        if (in_array($value, $this->routes)) return [];

        $existingRoutes = $this->routes;
        usort($existingRoutes, function (string $a, string $b) use ($value) {
            return levenshtein($a, $value) - levenshtein($b, $value);
        });
        $closest = $existingRoutes[0];

        return [
            RuleErrorBuilder::message("Route '$value' is not known")
                ->tip("Did you mean '$closest'?")
                ->build(),
        ];
    }

    /**
     * @return list<string>
     */
    private function getRouteNames(Routes $routes): array
    {
        $names = [];

        foreach ($routes->all as $route) {
            if (($name = $route->name) !== null) {
                $names[] = $name;
            }
        }

        return $names;
    }
}
