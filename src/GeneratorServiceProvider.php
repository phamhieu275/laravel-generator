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

        $this->publishes([
            $configPath => config_path('generator.php'),
        ], 'generator');

        $this->mergeConfigFrom($configPath, 'generator');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            'Bluecode\Generator\Commands\ControllerGeneratorCommand',
            'Bluecode\Generator\Commands\FactoryGeneratorCommand',
            'Bluecode\Generator\Commands\MigrationGeneratorCommand',
            'Bluecode\Generator\Commands\PublishTemplateCommand',

            'Bluecode\Generator\Commands\ModelGeneratorCommand',
            'Bluecode\Generator\Commands\ViewGeneratorCommand',

            'Bluecode\Generator\Commands\ProviderGeneratorCommand',
            'Bluecode\Generator\Commands\ResourceGeneratorCommand',
            'Bluecode\Generator\Commands\PackageGeneratorCommand',
        ]);
    }
}
