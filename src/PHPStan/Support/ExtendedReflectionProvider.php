<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan\Support;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\NullsafeMethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;

class ExtendedReflectionProvider
{
    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
    }

    /**
     * @return array{0: ClassReflection, 1: ReflectionMethod}|array{0: null, 1: ReflectionFunction}|null
     */
    public function resolveCallLike(
        FuncCall|MethodCall|NullsafeMethodCall|StaticCall $node,
        Scope $scope,
    ): ?array {
        try {
            if ($node instanceof FuncCall) {
                $name = $node->name;
                if (! ($name instanceof Name)) {
                    return null;
                }

                $function = $this->reflectionProvider->getFunction($name, $scope);
                $reflected = new ReflectionFunction($function->getName());

                return [null, $reflected];
            }

            $class = $this->reflectClass($node, $scope);
            $method = $this->reflectMethod($class, $node);
            if ($class === null && $method === null) {
                return null;
            }

            return [$class, $method];
        } catch (ReflectionException) {
            return null;
        }
    }

    private function reflectClass(
        MethodCall|NullsafeMethodCall|StaticCall $node,
        Scope $scope,
    ): ?ClassReflection {
        if ($node instanceof StaticCall) {
            $className = $node->class;
            if (! ($className instanceof Name)) {
                return null;
            }

            $fqn = $scope->resolveName($className);

            return $this->reflectionProvider->getClass($fqn);
        }

        $type = $scope->getType($node->var);
        $classes = $type->getReferencedClasses();
        if (count($classes) === 0) {
            return null;
        }

        return $this->reflectionProvider->getClass($classes[0]);
    }

    private function reflectMethod(
        ?ClassReflection $class,
        StaticCall|MethodCall|FuncCall|NullsafeMethodCall $node,
    ): ?ReflectionMethod {
        if ($class === null) {
            return null;
        }

        $methodName = $node->name;
        if (! ($methodName instanceof Identifier)) {
            return null;
        }

        return $class
            ->getNativeReflection()
            ->getMethod($methodName->name);
    }
}
