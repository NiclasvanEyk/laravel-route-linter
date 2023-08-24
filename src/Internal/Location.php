<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

final readonly class Location
{
    public function __construct(
        public string $file,
        public ?Position $position = null,
    ) {
    }
}
