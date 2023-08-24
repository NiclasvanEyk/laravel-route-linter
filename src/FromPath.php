<?php

namespace NiclasVanEyk\LaravelRouteLinter;

use Attribute;

/**
 * Signifies that the parameter should be resolved from the routes' path.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class FromPath
{
    public function __construct(public ?string $name = null)
    {
    }
}
