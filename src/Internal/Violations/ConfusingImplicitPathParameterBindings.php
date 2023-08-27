<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Violations;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;
use function array_map;
use function implode;

readonly final class ConfusingImplicitPathParameterBindings extends Violation
{
    public function __construct(
        RouteInformation $route,
        array $expected,
        array $actual
    ) {
        $link = "https://laravel.com/docs/routing#required-parameters";
        $expected = $this->displayOrderForViolationMessage($expected);
        $actual = $this->displayOrderForViolationMessage($actual);

        parent::__construct(
            implode(" ", [
                "The controller function parameters of <info>{$route}</info> are misleading.",
                "Their order in the path is <info>$expected</info>, but in the controller the order is <info>$actual</info>.",
                "See $link for more information.",
            ]),
            Confidence::Definite,
        );
    }

    /**
     * @param string[] $parameters
     * @return string
     */
    private function displayOrderForViolationMessage(array $parameters): string
    {
        $items = implode(', ', array_map(function (string $name) {
            return "\"$name\"";
        }, $parameters));

        return "[$items]";
    }
}
