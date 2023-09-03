<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Violations;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

final readonly class RouteShadowed extends Violation
{
    public function __construct(
        public RouteInformation $new,
        public RouteInformation $existing,
    ) {
        parent::__construct(
            "The route `$new` clashes with the existing route `$existing`. Register it earlier to prevent this issue.",
            Confidence::Probably);
    }
}
