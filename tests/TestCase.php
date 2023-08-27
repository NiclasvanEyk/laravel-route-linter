<?php

namespace NiclasVanEyk\LaravelRouteLinter\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use NiclasVanEyk\LaravelRouteLinter\LaravelRouteLinterServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'NiclasVanEyk\\LaravelRouteLinter\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelRouteLinterServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-route-linter_table.php.stub';
        $migration->up();
        */
    }
}
