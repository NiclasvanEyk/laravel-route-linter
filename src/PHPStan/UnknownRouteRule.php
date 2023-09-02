<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use function class_implements;

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

    public function __construct(Routes $routes)
    {
        $mapped = [];
        foreach ($routes->all as $route) {
            $name = $route->name;
            if ($name === null) continue;

            $mapped[$name] = $route->pathParameters;
        }

        $this->routes = $mapped;
    }

    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (!($node instanceof MethodCall)) return [];

        $classes = $scope->getType($node->var)->getReferencedClasses();
        if (!$this->referencesUrlGenerator($classes)) return [];

        $methodName = $node->name->name;

    }

    /**
     * @param string|string[] $classes
     */
    private function referencesUrlGenerator(string|array $classes): bool
    {
        foreach ($classes as $class) {
            foreach (class_implements($class) as $interface) {
                if ($interface === '\Illuminate\Contracts\Routing\UrlGenerator') {
                    return true;
                }
            }
        }

        return false;
    }
}
