<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests;

use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePath;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Constant;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\ConfusingImplicitPathParameterBindings;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\ConfusingImplicitPathParameterRule;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\ViolationContainer;
use NiclasVanEyk\LaravelRouteLinter\Tests\Fixtures\Foo;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use ReflectionClass;

class ConfusingImplicitPathParameterRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new ConfusingImplicitPathParameterRule(
            ViolationContainer::filledWith(
                [
                    new ConfusingImplicitPathParameterBindings(
                        route: new RouteInformation(
                            ['GET', 'HEAD'],
                            'test',
                            new RoutePath('/foo', [new Constant('foo')]),
                            [],
                            (new ReflectionClass(Foo::class))->getMethod('foo'),
                        ),
                        expected: ['foo', 'bar'],
                        actual: ['bar', 'foo'],
                    ),
                ],
            )
        );
    }

    public function testFooBar(): void
    {
        $this->analyse([__DIR__.'/fixtures/Foo.php'], [
            [
                "The controller function parameters of `GET|HEAD /foo` are misleading. Their order in the path is `[\"foo\", \"bar\"]`, but in the controller the order is `[\"bar\", \"foo\"]`.\n    💡 See https://laravel.com/docs/routing#required-parameters for more information.",
                7,
            ],
        ]);
    }
}
