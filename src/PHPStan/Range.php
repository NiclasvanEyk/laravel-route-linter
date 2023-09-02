<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use PhpParser\Node;
use ReflectionFunction;
use ReflectionMethod;

readonly final class Range
{
    public function __construct(
        public Position $begin,
        public Position $end,
    )
    {
    }

    public static function of(
        Node|ReflectionMethod|ReflectionFunction $subject,
    ) {
        if ($subject instanceof Node) {
            return new self(
                begin: new Position(line: $subject->getStartLine(), column: 0),
                end: new Position(line: $subject->getEndLine(), column: 0),
            );
        }

        return new self(
            begin: new Position(line: $subject->get(), column: 0),
            end: new Position(line: $subject->getEndLine(), column: 0),
        );
    }
}
