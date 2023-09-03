<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Support\Facades\URL as UrlFacade;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\Attributes\RouteName;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\ExtendedReflectionProvider;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\Reflection;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\CallLike;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use function class_implements;
use function count;

/**
 * A 'registry' for functions that deal with routes and URL generation.
 */
class RoutingFunctions
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private ExtendedReflectionProvider $extendedReflectionProvider,
    ) {
    }

    /**
     * TODO: Provide documentation
     *
     * @see https://sourcegraph.com/search?q=repo:%5Egithub%5C.com/laravel/framework%24+%28%24route%2C+%24parameters+%3D+%5B%5D
     * @see https://sourcegraph.com/search?q=repo%3A%5Egithub%5C.com%2Flaravel%2Fframework%24+%24name%2C+%24parameters+%3D+%5B%5D
     * @var list<array{0: class-string, 1: string}|array{0: null, 1: callable-string}>
     */
    private array $routeMethods = [
        [null, 'route'],
        [null, 'action'], // Maybe do a special case for this one, since the first argument also could be a callable?
        [null, 'to_route'],
        [Redirector::class, 'route'],
        [Redirector::class, 'signedRoute'],
        [ResponseFactory::class, 'redirectToRoute'],
        [RouteUrlGenerator::class, 'to'],
        [UrlGenerator::class, 'route'],
        [UrlGenerator::class, 'signedRoute'],
        [UrlFacade::class, 'route'],
        [UrlFacade::class, 'signedRoute'],
        ['URL', 'route'],
        ['URL', 'signedRoute'],
    ];

    public function getRouteNameArgument(
        CallLike $node,
        Scope $scope,
    ): ?Arg {
        $result = $this->extendedReflectionProvider->resolveCallLike($node, $scope);
        if ($result === null) return null;
        [$class, $function] = $result;

        // Attempt 1: We look if one of the parameters is annotated with our
        // marker attribute.
        $argumentNode = $this->getRouteNameViaAttribute($node, $function);
        if ($argumentNode !== null) return $argumentNode;

        // If this is not the case, we check a set of functions/methods that are
        // known to accept route names. If this is not the case, we can safely
        // return early here.
        if (!$this->isWellKnownRouteFunction($class, $function)) return null;

        // If the function/method is well known, we assume that the first
        // parameter represents the route name.
        // TODO: Maybe make an exception for `action`.
        return $node->getRawArgs()[0];
    }

    private function getRouteNameViaAttribute(
        FuncCall|MethodCall|NullsafeMethodCall|StaticCall $node,
        ReflectionFunctionAbstract $method,
    ): ?Arg {
        foreach ($method->getParameters() as $index => $parameter) {
            if (count($parameter->getAttributes(RouteName::class)) > 0) {
                $arg = $node->args[$index];
                if ($arg) return $arg;
            }
        }

        return null;
    }

    private function isWellKnownRouteFunction(
        ?ClassReflection $class,
        ReflectionFunctionAbstract $function,
    ): bool {
        $searchedClassName = $class?->getName();
        $searchedFunctionName = $function->getName();

        foreach ($this->routeMethods as [$fqn, $method]) {
            $nameMatchesFqn = $fqn === $searchedClassName;
            $implementsFqn = Reflection::isInterface($fqn) && $class?->implementsInterface($fqn);
            if (!($nameMatchesFqn || $implementsFqn)) continue;

            if ($method === $searchedFunctionName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the argument nodes containing the route name and parameters.
     *
     * This is mostly relevant for URL-generating functions like {@link route()}
     * or {@link UrlGenerator::route}.
     *
     * @return false|array{0: Arg, 1: Arg}
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

        // TODO: Catch if no first or second arg was provided
        return [$node->args[0], $node->args[1]];
    }

    /**
     * @param string[] $classes
     */
    private function referencesUrlGenerator(array $classes): bool
    {
        foreach ($classes as $class) {
            if ($this->satisfiesInterface($class, 'Illuminate\Contracts\Routing\UrlGenerator')) {
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
    private function satisfiesInterface(string $class, string $interface): bool
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
