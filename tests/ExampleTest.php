<?php

use Illuminate\Support\Facades\Artisan;
use NiclasVanEyk\LaravelRouteLinter\Commands\LintRoutesCommand;

it('can test', function () {
    Artisan::call(LintRoutesCommand::class);
});
