<?php namespace Bluecode\Generator;

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
        $configPath = __DIR__.'/../config/generator.php';

        $this->publishes([
            $configPath => config_path('generator.php'),
        ], 'config');

        $this->mergeConfigFrom($configPath, 'generator');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Bluecode\Generator\Commands\PublishTemplateCommand',
            'Bluecode\Generator\Commands\MigrationMakeCommand',
            'Bluecode\Generator\Commands\ModelMakeCommand',
            'Bluecode\Generator\Commands\ControllerMakeCommand',
            'Bluecode\Generator\Commands\ViewMakeCommand',
            'Bluecode\Generator\Commands\FactoryMakeCommand',
            'Bluecode\Generator\Commands\ProviderMakeCommand',
            'Bluecode\Generator\Commands\ResourceMakeCommand',
            'Bluecode\Generator\Commands\PackageNewCommand'
        );
    }
}
