<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

readonly final class Position
{
    public function __construct(
        public int $line,
        public int $column,
    ) {
    }
}
