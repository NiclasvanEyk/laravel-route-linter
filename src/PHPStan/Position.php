<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

final readonly class Position
{
    public function __construct(
        public int $line,
        public int $column,
    ) {
    }
}
