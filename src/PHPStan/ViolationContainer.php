<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\RoutePathParameterNamesLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter\ShadowedRouteLinter;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linters;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;
use function array_filter;
use function array_values;

final class ViolationContainer
{
    /**
     * @param list<Violation> $violations
     * @param bool $resolve
     */
    public function __construct(
        private array $violations = [],
        private bool $resolve = true,
    ) {
        if ($this->resolve) {
            $this->resolve();
        }
    }

    public static function filledWith(array $violations): self
    {
        return new self($violations, resolve: false);
    }

    public function resolve(): void
    {
        $this->violations = (new Linters(
            new ShadowedRouteLinter(),
            new RoutePathParameterNamesLinter(),
        ))->lint(RouteInformation::all());
    }

    /**
     * @template V of Violation
     * @param class-string<V> $type
     * @return list<V>
     */
    public function violations(string $type): array
    {
        return array_values(
            array_filter(
                $this->violations,
                fn (Violation $v) => $v::class === $type,
            ),
        );
    }
}
