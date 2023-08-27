<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal\Violations;

use NiclasVanEyk\LaravelRouteLinter\Internal\Confidence;
use NiclasVanEyk\LaravelRouteLinter\Internal\RouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Violation;

final readonly class RouteShadowed extends Violation
{
    public function __construct(
        RouteInformation $new,
        RouteInformation $existing,

    ) {
        parent::__construct(
            "The route <info>{$new}</info> clashes with the existing route <info>{$existing}</info>. Register it earlier to prevent this issue.",
            Confidence::Probably);
    }
}
