<?php

namespace NiclasVanEyk\LaravelRouteLinter;

use Attribute;

/**
 * Excludes a class/method/parameter from the linting process.
 */
#[Attribute]
final readonly class Ignore
{
    public function __construct(public ?string $reason = null)
    {
    }
}
