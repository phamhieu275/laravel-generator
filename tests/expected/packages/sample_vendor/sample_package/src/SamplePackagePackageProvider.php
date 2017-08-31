<?php

namespace SampleVendor\SamplePackage;

use Illuminate\Support\ServiceProvider;

class SamplePackagePackageProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware(['web'])
            ->namespace('SampleVendor\SamplePackage\Http\Controllers')
            ->group(__DIR__ . '/routes.php');

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'sample_package');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
