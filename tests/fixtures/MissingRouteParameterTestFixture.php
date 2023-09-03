<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests\Fixtures;

use Illuminate\Contracts\Routing\UrlGenerator;

final readonly class MissingRouteParameterTestFixture
{
    public function method(UrlGenerator $url)
    {
        $url->route(
            'articles.comments.delete',
            ['article' => 1],
        );
    }
}
