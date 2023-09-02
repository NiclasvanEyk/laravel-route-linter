<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use function class_implements;

/**
 * A 'registry' for functions that deal with routes and URL generation.
 */
class RoutingFunctions
{
    /**
     * Returns the argument nodes containing the route name and parameters.
     *
     * This is mostly relevant for URL-generating functions like {@link route()}
     * or {@link \Illuminate\Contracts\Routing\UrlGenerator::route()}.
     *
     * @return false|array{0: Node\Arg, 1: Node\Arg}
     */
    public function extractRouteAndParameterNameNodes(
        Node $node,
        Scope $scope,
    ): ?array
    {
        if (!($node instanceof MethodCall)) return false;

        // TODO: add `route` and `URL::signedRoute`, `to_route`, ...
        // See https://sourcegraph.com/search?q=repo:%5Egithub%5C.com/laravel/framework%24+%28%24route%2C+%24parameters+%3D+%5B%5D
        // and https://sourcegraph.com/search?q=repo%3A%5Egithub%5C.com%2Flaravel%2Fframework%24+%24name%2C+%24parameters+%3D+%5B%5D

        $classes = $scope->getType($node->var)->getReferencedClasses();
        if (!$this->referencesUrlGenerator($classes)) return false;
        if ($node->name->name !== 'route') return false;

        // TODO: Catch if no second arg was provided
        return [$node->args[0], $node->args[1]];
    }

    /**
     * @param string[] $classes
     */
    private function referencesUrlGenerator(array $classes): bool
    {
        foreach ($classes as $class) {
            if ($this->satifiesInterface($class, 'Illuminate\Contracts\Routing\UrlGenerator')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param class-string $class
     * @param class-string $interface
     * @return bool
     */
    private function satifiesInterface(string $class, string $interface): bool
    {
        if ($class === $interface) return true;

        foreach (class_implements($class) as $implemented) {
            if ($interface === $implemented) {
                return true;
            }
        }

        return false;
    }

}
