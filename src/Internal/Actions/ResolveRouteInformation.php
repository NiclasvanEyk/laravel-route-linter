<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Actions;

use Closure;
use Exception;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;
use LogicException;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePath;
use ReflectionClass;
use ReflectionFunction;

use function array_map;
use function explode;
use function is_string;

final readonly class ResolveRouteInformation
{
    /**
     * @return list<RouteInformation>
     */
    public function __invoke(Router $router = null): array
    {
        $router ??= resolve('router');

        $routes = $router->getRoutes();
        if ((! $routes instanceof RouteCollection)) {
            throw new Exception("Compiled routes can't be validated!");
        }

        return array_map(function (Route $route) {
            $parameters = $this->determineRouteParameters($route);
            $compiled = $route->toSymfonyRoute()->compile();
            $variables = $compiled->getPathVariables();

            return new RouteInformation(
                RoutePath::fromCompiledSymfonyRoute($route->uri, $compiled),
                $variables,
                $parameters,
            );
        }, iterator_to_array($routes->getIterator()));
    }

    private function determineRouteParameters(Route $route): array
    {
        $uses = $route->getAction('uses');

        if ($uses instanceof Closure) {
            return (new ReflectionFunction($uses))->getParameters();
        }

        if (is_string($uses)) {
            [$controller, $method] = explode('@', $uses);

            return (new ReflectionClass($controller))
                ->getMethod($method)
                ->getParameters();
        }

        throw new LogicException();
    }
}
