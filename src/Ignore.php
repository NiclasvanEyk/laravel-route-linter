<?php

namespace NiclasVanEyk\LaravelRouteLinter;

use Attribute;

/**
 * Excludes a class/method/parameter from the linting process.
 */
#[Attribute]
readonly final class Ignore
{
    public function __construct(public ?string $reason = null)
    {
    }
}
