<?php

namespace NiclasVanEyk\LaravelRouteLinter\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use NiclasVanEyk\LaravelRouteLinter\Internal\Actions\ResolveRouteInformation;
use NiclasVanEyk\LaravelRouteLinter\Internal\Linters;

use function class_basename;

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
            $this->components->error("Potential problems found:");
            $this->display($violations);

            return self::FAILURE;
        }

        $this->info('All routes are valid!');

        return self::SUCCESS;
    }

    private function display(array $violations): void
    {
        foreach ($violations as $violation) {
            $kind = class_basename($violation::class);
            $this->line("<error>$kind</error>");
            $this->line($violation->message);
            $this->newLine();
        }
    }
}
