<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

final readonly class Linters implements Linter
{
    /**
     * @var list<Linter>
     */
    private array $linters;

    public function __construct(Linter ...$linters)
    {
        $this->linters = $linters;
    }

    public function lint(array $routes): array
    {
        $violations = [];

        foreach ($this->linters as $linter) {
            $violations = array_merge($violations, $linter->lint($routes));
        }

        return $violations;
    }
}
