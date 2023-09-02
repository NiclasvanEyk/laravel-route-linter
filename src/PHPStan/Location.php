<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use PhpParser\Node;
use ReflectionFunction;
use ReflectionMethod;

readonly final class Location
{
    public function __construct(
        public string $file,
        public Range $range,
    ) {
    }

    public function of(Node|ReflectionFunction|ReflectionMethod $node): ?self
    {
        $startLine = $node->getStartLine();
        $node->getEnd();
    }
}
