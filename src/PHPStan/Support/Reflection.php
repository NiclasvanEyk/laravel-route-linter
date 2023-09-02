<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan\Support;

use PhpParser\Node;

readonly final class Reflection
{
    public static function constantStringValueOf(Node $node): ?string
    {
        if ($node instanceof Node\Scalar\String_) {
            return $node->value;
        }

        // TODO: Class constants or enum constants could be also statically resolved

        return null;
    }
}
