<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

final readonly class Violation
{
    public function __construct(
        public string $message,
        public Confidence $confidence,
        public Location $location,
    ) {
    }
}
