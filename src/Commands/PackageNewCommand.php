<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\ManipulatesPackageTrait;
use Bluecode\Generator\Traits\InteractsWithUserTrait;

class PackageNewCommand extends Command
{
    use TemplateTrait;
    use ManipulatesPackageTrait;
    use InteractsWithUserTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'generator:package:new
        {vendor : The vendor part of the namespace}
        {package : The name of package for the namespace}
        {--i|interactive : Interactive mode}
        {--model= : The model class name}
        {--path= : The location where the package should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package.';

    /**
     * Create a new package command instance.
     *
     * @param  \Illuminate\Support\Composer  $composer
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendorName = $this->getVendor();
        $packageName = $this->getPackage();

        $relativePath = $this->getRelativePath($vendorName, $packageName);
        $packagePath = $this->getPackagePath($relativePath);

        $rootNamespace = $vendorName . '\\' . $packageName;
        $this->generateSkeleton($rootNamespace, $packageName, $relativePath, $packagePath);

        if ($this->option('model')) {
            $this->generateResource($this->option('model'), $rootNamespace, $relativePath, $packagePath);
        }

        $this->composer->dumpAutoloads();
        $this->info('Finished. Are you ready to write awesome package?');
    }

    /**
     * Gets the relative path.
     *
     * @param string $vendorName The vendor name
     * @param string $packageName The package name
     * @return string
     */
    private function getRelativePath($vendorName, $packageName)
    {
        if ($this->option('path')) {
            return trim($this->option('path'), '/');
        }

        $vendorFolderName = $this->getVendorFolderName($vendorName);
        $packageFolderName = $this->getPackageFolderName($packageName);

        return "{$vendorFolderName}/{$packageFolderName}";
    }

    /**
     * Gets the package path.
     *
     * @param string $relativePath The relative path
     * @return string
     */
    private function getPackagePath($relativePath)
    {
        return config('generator.package_base_path') . '/' . $relativePath;
    }

    /**
     * Create skeleton directory, composer.json file and provider file.
     *
     * @param string $rootNamespace The root namespace
     * @param string $packageName The package name
     * @param string $relativePath The relative path
     * @param string $packagePath The package path
     * @return void
     */
    private function generateSkeleton($rootNamespace, $packageName, $relativePath, $packagePath)
    {
        $this->createPackageFolder($packagePath);

        $this->createComposerFile($this->getTemplatePath(), $packagePath, $packageName);

        $providerArguments = [
            'name' => studly_case($packageName) . 'PackageProvider',
            '--namespace' => $rootNamespace,
            '--path' => "{$relativePath}/src"
        ];

        if ($this->option('model')) {
            $providerArguments = array_merge($providerArguments, [
                '--model' => $this->option('model'),
                '--view' => $this->getViewNamespace($rootNamespace)
            ]);
        }
        $this->call('generator:make:provider', $providerArguments);
    }

    /**
     * Generate CRUD with given model name
     *
     * @param string $model The model
     * @param string $rootNamespace The root namespace
     * @param string $relativePath The relative path
     * @param string $packagePath The package path
     * @return void
     */
    private function generateResource($model, $rootNamespace, $relativePath, $packagePath)
    {
        $this->createRouteFile($packagePath);

        $this->call('generator:make:model', [
            'name' => $model,
            '--namespace' => $rootNamespace,
            '--path' => $relativePath . '/src/Models'
        ]);

        $this->call('generator:make:controller', [
            'name' => $model . 'Controller',
            '--namespace' => $rootNamespace,
            '--path' => $relativePath . '/src/Http/Controllers',
            '--model' => $rootNamespace . '\Models\\' . $model,
            '--skipCheckModel' => true,
            '--view' => $this->getViewNamespace($rootNamespace) . '::'
        ]);

        $this->call('generator:make:view', [
            'name' => $model,
            '--path' => $relativePath . '/src/resources/views'
        ]);
    }

    /**
     * Gets the view namespace.
     *
     * @param string $rootNamespace The root namespace
     * @return string
     */
    private function getViewNamespace($rootNamespace)
    {
        return strtolower(str_replace('\\', '.', $rootNamespace));
    }
}
