<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken;

final readonly class Variable
{
    public function __construct(public string $name)
    {
    }
}
