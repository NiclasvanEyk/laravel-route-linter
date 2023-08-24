<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

readonly final class Location
{
    public function __construct(
        public string $file,
        public ?Position $position = null,
    ) {
    }
}
