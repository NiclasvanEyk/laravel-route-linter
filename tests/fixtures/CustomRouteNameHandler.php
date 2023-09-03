<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests\Fixtures;

use NiclasVanEyk\LaravelRouteLinter\PHPStan\Attributes\RouteName;

use function route;

class CustomRouteNameHandler
{
    public static function staticMethod(#[RouteName] string $name): string
    {
        return route($name);
    }

    public static function staticCall(): void
    {
        self::staticMethod('foo');
    }

    public function method(#[RouteName] string $name): string
    {
        return route($name);
    }

    public function methodCall(): void
    {
        $this->method('test');
    }
}
