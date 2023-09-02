<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests\Fixtures;

use Illuminate\Contracts\Routing\UrlGenerator;

readonly final class MissingRouteParameterTestFixture
{
    public function method(UrlGenerator $url)
    {
        $url->route(
            'articles.comments.delete',
            ['article' => 1],
        );
    }
}
