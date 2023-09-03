<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\ConfusingImplicitPathParameterBindings;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\ConfusingImplicitPathParameterBindings as Violation;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ExtendedMethodReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

final readonly class ConfusingImplicitPathParameterRule implements Rule
{
    /**
     * @var Violation[]
     */
    private array $violations;

    public function __construct(ViolationContainer $violations)
    {
        $this->violations = $violations->violations(ConfusingImplicitPathParameterBindings::class);
    }

    public function getNodeType(): string
    {
        return Node\Stmt\ClassMethod::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! ($node instanceof Node\Stmt\ClassMethod)) {
            return [];
        }

        $method = $scope->getClassReflection()?->getMethod($node->name->name, $scope);
        if (! ($method instanceof ExtendedMethodReflection)) {
            return [];
        }

        $errors = [];
        foreach ($this->violations as $violation) {
            $handler = $violation->route->handler;
            if ($handler->getDeclaringClass()->getName() !== $method->getDeclaringClass()->getName()) {
                continue;
            }
            if ($handler->getName() !== $method->getName()) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message($violation->getMessage('`', '`'))
                ->tip($violation->getTip())
                ->build();
        }

        return $errors;
    }
}
