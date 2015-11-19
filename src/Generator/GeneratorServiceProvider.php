<?php namespace Bluecode\Generator;

use Illuminate\Support\ServiceProvider;
use Bluecode\Generator\Commands\APIGeneratorCommand;
use Bluecode\Generator\Commands\PublisherCommand;
use Bluecode\Generator\Commands\ScaffoldAPIGeneratorCommand;
use Bluecode\Generator\Commands\ScaffoldGeneratorCommand;
use Bluecode\Generator\Commands\ModelGeneratorCommand;
use Bluecode\Generator\Commands\MigrateGeneratorCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../../../config/generator.php';

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
        $this->app->singleton('generate.publish', function ($app) {
            return new PublisherCommand();
        });

        $this->app->singleton('generate.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('generate.model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('generate.migrate', function ($app) {
            return new MigrateGeneratorCommand();
        });

        $this->commands([
            'generate.publish',
            'generate.scaffold',
            'generate.model',
            'generate.migrate',
        ]);
    }
}
