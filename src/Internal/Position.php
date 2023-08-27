<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

final readonly class Position
{
    public function __construct(public int $line, public int $column)
    {
    }
}
