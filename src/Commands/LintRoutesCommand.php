<?php

namespace NiclasVanEyk\LaravelRouteLinter\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linters;

class LintRoutesCommand extends Command
{
    public $signature = 'route:lint';

    public $description = 'My command';

    public function handle(
        Linters $linters,
        ResolveRouteInformation $resolveRouteInformation,
        Router $router,
    ): int {
        $routes = $resolveRouteInformation($router);
        $violations = $linters->lint($routes);

        if (count($violations) > 0) {
            $this->display($violations);

            return self::FAILURE; // errornous?
        }

        $this->info("All routes are valid!");
        return self::SUCCESS;
    }

    private function display(array $violations): void
    {
    }
}
