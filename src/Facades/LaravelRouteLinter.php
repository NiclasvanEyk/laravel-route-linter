<?php

namespace NiclasVanEyk\LaravelRouteLinter\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NiclasVanEyk\LaravelRouteLinter\LaravelRouteLinter
 */
class LaravelRouteLinter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \NiclasVanEyk\LaravelRouteLinter\LaravelRouteLinter::class;
    }
}
