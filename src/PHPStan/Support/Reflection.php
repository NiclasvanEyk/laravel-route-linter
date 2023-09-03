<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan\Support;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use ReflectionClass;
use ReflectionException;

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

    public static function isInterface(?string $fqn): bool
    {
        if ($fqn === null) return false;

        try {
            $reflected = new ReflectionClass($fqn);
            return $reflected->isInterface();
        } catch (ReflectionException) {
            return false;
        }
    }
}
