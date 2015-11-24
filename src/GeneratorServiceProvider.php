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
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Bluecode\Generator\Commands\PublishCommand',
            'Bluecode\Generator\Commands\MigrationMakeCommand',
            'Bluecode\Generator\Commands\ModelMakeCommand',
            'Bluecode\Generator\Commands\ScaffoldMakeCommand',
            'Bluecode\Generator\Commands\FactoryMakeCommand',
            'Bluecode\Generator\Commands\ResourceMakeCommand'
        );
    }
}
