<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Violations;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

use function array_map;
use function implode;

final readonly class ConfusingImplicitPathParameterBindings extends Violation
{
    public array $wrongParameterNames;

    public function __construct(
        public RouteInformation $route,
        public array $expected,
        public array $actual,
    ) {
        $misplaced = [];
        for ($index = 0; $index < max(count($this->expected), count($this->actual)); $index++) {
            $expectedParameter = $this->expected[$index] ?? null;
            $actualParameter = $this->actual[$index] ?? null;
            if ($expectedParameter === $actualParameter) continue;

            $misplaced[] = $actualParameter;
        }
        $this->wrongParameterNames = $misplaced;

        parent::__construct(
            $this->getMessage('<info>', '</info>') . ' ' . $this->getTip(),
            Confidence::Definite,
        );
    }

    public function getTip(): string
    {
        return "See https://laravel.com/docs/routing#required-parameters for more information.";
    }

    public function getMessage(string $start, string $end)
    {
        $expected = $this->displayOrderForViolationMessage($this->expected);
        $actual = $this->displayOrderForViolationMessage($this->actual);
        $wrap = fn ($subject) => $start . $subject . $end;

        return implode(' ', [
            "The controller function parameters of {$wrap($this->route)} are misleading.",
            "Their order in the path is `$expected`, but in the controller the order is `$actual`.",
        ]);
    }

    /**
     * @param  string[]  $parameters
     */
    private function displayOrderForViolationMessage(array $parameters): string
    {
        $items = implode(', ', array_map(function (string $name) {
            return "\"$name\"";
        }, $parameters));

        return "[$items]";
    }
}
