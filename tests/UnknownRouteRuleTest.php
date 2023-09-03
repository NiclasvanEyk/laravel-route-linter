<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests;

use NiclasVanEyk\LaravelRouteLinter\PHPStan\RoutingFunctions;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\Support\ExtendedReflectionProvider;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\UnknownRouteRule;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class UnknownRouteRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        $reflector = self::getContainer()->getByType(ReflectionProvider::class);

        return new UnknownRouteRule(
            ['articles.comments.delete' => ['article', 'comment']],
            new RoutingFunctions(
                $reflector,
                new ExtendedReflectionProvider($reflector),
            ),
        );
    }

    public function testItWorks(): void
    {
        $this->analyse([
            __DIR__.'/Fixtures/CustomRouteNameHandler.php',
        ], []);
    }
}
