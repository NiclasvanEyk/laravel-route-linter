<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\Reflection;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use function array_diff;
use function array_key_exists;
use function array_values;
use function implode;
use function is_array;

/**
 * Validates that all path parameters are passed when generating URLs.
 */
class MissingRouteParameterRule implements Rule
{
    /**
     * @var array<string,list<string>> Route names as keys and their path
     * parameters as values. Routes without names are not included.
     */
    public array $routes = [];

    /**
     * @param Routes|array<string,list<string>> $routes
     */
    public function __construct(
        Routes|array $routes,
        private readonly RoutingFunctions $routingFunctions,
    ) {
        $this->routes = is_array($routes)
            ? $routes
            : $this->getRouteParameterNamesByRouteName($routes);
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $result = $this
            ->routingFunctions
            ->extractRouteAndParameterNameNodes($node, $scope);
        if (!$result) return [];

        // Resolve route name
        [$nameNode, $parametersNode] = $result;
        $name = Reflection::constantStringValueOf($nameNode->value);
        // Note: If we don't know the route, it is likely an error.
        // However, we don't report it here, this is the job of a separate rule.
        if ($name === null) return [];

        // Resolve expected and actually passed route parameters
        if (!array_key_exists($name, $this->routes)) return [];
        $expected = $this->routes[$name];
        if (count($expected) === 0) return [];
        $actual = UrlGenerationPathParameters::tryFromNode(
            $parametersNode->value,
        );
        if ($actual === null) return [];

        // Validate parameters
        $missing = array_values(
            array_diff($expected, $actual->matchedTo($expected)),
        );
        if (count($missing) === 0) return [];

        return $this->buildErrors($parametersNode, $missing);
    }


    /**
     * @return array<string,list<string>>
     */
    private function getRouteParameterNamesByRouteName(Routes $routes): array
    {
        $mapped = [];
        foreach ($routes->all as $route) {
            $name = $route->name;
            if ($name === null) continue;

            $mapped[$name] = $route->pathParameters;
        }

        return $mapped;
    }

    /**
     * @param Node $parametersNode
     * @param list<string> $missing
     * @return list<RuleError>
     */
    private function buildErrors(mixed $parametersNode, array $missing): array
    {
        $asString = implode(", ", $missing);

        return [
            RuleErrorBuilder::message("Missing route path parameters: $asString")
                ->line($parametersNode->getLine())
                ->build()
        ];
    }
}
