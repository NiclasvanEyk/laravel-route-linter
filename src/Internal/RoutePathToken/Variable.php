<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken;

readonly final class Variable
{
    public function __construct(public string $name)
    {
    }
}
