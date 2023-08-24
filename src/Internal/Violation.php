<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

readonly final class Violation
{
    public function __construct(
        public string $message,
        public Confidence $confidence,
        public Location $location,
    ) {
    }
}
