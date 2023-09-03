<?php

namespace NiclasVanEyk\LaravelRouteLinter\PHPStan;

use NiclasVanEyk\LaravelRouteLinter\Internal\Violations\RouteShadowed;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

final readonly class ShadowedRouteRule implements Rule
{
    /**
     * @var RouteShadowed[]
     */
    private array $violations;

    public function __construct(ViolationContainer $violations)
    {
        $this->violations = $violations->violations(RouteShadowed::class);
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (! ($node instanceof Node\FunctionLike)) {
            return [];
        }

        $errors = [];
        foreach ($this->violations as $violation) {
            $handler = $violation->new->handler;
            if ($handler->getFileName() !== $scope->getFile()) {
                continue;
            }
            if ($handler->getStartLine() !== $node->getStartLine()) {
                continue;
            }
            if ($handler->getEndLine() !== $node->getEndLine()) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message($violation->message)->build();
        }

        return $errors;
    }
}
