<?php

namespace Bluecode\Generator;

use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/generator.php';
        $this->mergeConfigFrom($configPath, 'generator');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $configPath => config_path('generator.php'),
            ], 'laravel-generator.config');

            $templatePath = __DIR__ . '/../templates';
            $this->publishes([
                $templatePath => config('generator.path.templates'),
            ], 'laravel-generator.template');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\MigrationGeneratorCommand::class,
                Commands\ControllerGeneratorCommand::class,
                Commands\ModelGeneratorCommand::class,
                Commands\ViewGeneratorCommand::class,

                Commands\MvcGeneratorCommand::class,
                Commands\ProviderGeneratorCommand::class,
                Commands\PackageGeneratorCommand::class,

                Commands\AllMigrationGeneratorCommand::class,
                Commands\AllModelGeneratorCommand::class,
                Commands\AllMvcGeneratorCommand::class,
            ]);
        }
    }
}
