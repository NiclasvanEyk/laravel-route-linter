<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan\Support;

use PhpParser\Node;
use ReflectionClass;
use ReflectionException;

final readonly class Reflection
{
    public static function constantStringValueOf(Node $node): ?string
    {
        if ($node instanceof Node\Scalar\String_) {
            return $node->value;
        }

        // TODO: Class constants or enum constants could be also statically resolved

        return null;
    }

    public static function isInterface(?string $fqn): bool
    {
        if ($fqn === null) {
            return false;
        }

        try {
            $reflected = new ReflectionClass($fqn);

            return $reflected->isInterface();
        } catch (ReflectionException) {
            return false;
        }
    }
}
