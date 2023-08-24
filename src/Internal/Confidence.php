<?php

namespace NiclasVanEyk\LaravelRouteLinter\Internal;

enum Confidence: int
{
    case Potential = 1;
    case Maybe = 2;
    case Probably = 3;
    case Definite = 4;
}
