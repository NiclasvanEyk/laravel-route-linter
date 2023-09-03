<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests;

use NiclasVanEyk\LaravelRouteLinter\PHPStan\MissingRouteParameterRule;
use NiclasVanEyk\LaravelRouteLinter\PHPStan\RoutingFunctions;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

class MissingRouteParameterRuleTest extends RuleTestCase
{
    protected function getRule(): Rule
    {
        return new MissingRouteParameterRule([
            'articles.comments.delete' => ['article', 'comment'],
        ], new RoutingFunctions);
    }

    public function testItWorks(): void
    {
        $this->analyse([
            __DIR__.'/Fixtures/MissingRouteParameterTestFixture.php',
        ], [['Missing route path parameters: comment', 13]]);
    }
}
