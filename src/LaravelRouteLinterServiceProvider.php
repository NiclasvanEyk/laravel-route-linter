<?php

namespace NiclasVanEyk\LaravelRouteLinter;

use NiclasVanEyk\LaravelRouteLinter\Commands\LintRoutesCommand;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linter;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linters;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRouteLinterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-route-linter')
            ->hasConfigFile()
            ->hasCommand(LintRoutesCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->tag([
            Linter\RoutePathParameterNamesLinter::class,
            Linter\ShadowedRouteLinter::class,
        ], Linter::class);

        $this->app
            ->when(Linters::class)
            ->needs(Linter::class)
            ->giveTagged(Linter::class);
    }
}
