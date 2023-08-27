<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Constant;
use NiclasVanEyk\LaravelRouteLinter\Internal\RoutePathToken\Variable;
use Symfony\Component\Routing\CompiledRoute;

use function array_reverse;
use function explode;

final readonly class RoutePath
{
    /**
     * @param  list<Constant|Variable>  $segments
     */
    public function __construct(
        public string $pattern,
        public array $segments,
    ) {
    }

    public static function fromCompiledSymfonyRoute(
        string $pattern,
        CompiledRoute $route
    ): self {
        $wrappedTokens = [];
        foreach (array_reverse($route->getTokens()) as $token) {
            if ($token[0] === 'variable') {
                $wrappedTokens[] = new Variable($token[3]);
            } else {
                foreach (explode('/', $token[1]) as $constantSegment) {
                    if (trim($constantSegment) === '') {
                        continue;
                    }
                    $wrappedTokens[] = new Constant(trim($constantSegment));
                }
            }
        }

        return new self(
            $pattern,
            $wrappedTokens,
        );
    }
}
