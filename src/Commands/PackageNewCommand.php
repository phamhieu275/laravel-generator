<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
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
        {--path= : The location where the package should be created.}
        {--model= : The model class name}';

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
    public function __construct(Composer $composer, Filesystem $files)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->files = $files;
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

        $this->info('Running composer dump-autoload. Please wait a minute.');
        $this->composer->dumpAutoloads();

        $this->comment('Please copy the following line into config/app.php file.');
        $this->info($rootNamespace . '\\' . studly_case($packageName) . 'PackageProvider::class');
        $this->confirm('Have you done?', true);
        $this->info('Finished. Are you ready to write awesome package?');
    }

    /**
     * Gets the relative path.
     *
     * @param string $vendorName The vendor name
     * @param string $packageName The package name
     * @return string
     */
    protected function getRelativePath($vendorName, $packageName)
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
    protected function getPackagePath($relativePath)
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

        $this->createComposerFile($packagePath, $packageName);

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
     * Creates a composer file.
     *
     * @param string $packagePath The package path
     * @param string $packageName The package name
     * @return void
     */
    private function createComposerFile($packagePath, $packageName)
    {
        $filePath = $packagePath . '/composer.json';
        if ($this->files->exists($filePath)) {
            return;
        }

        $stubPath = $this->getTemplatePath() . '/package/composer.json.stub';
        $stub = str_replace('DummyPackageName', $packageName, $this->files->get($stubPath));

        $this->files->put($filePath, $stub);

        $this->info('composer.json created successfully.');
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
        $this->createRouteFile($packagePath, $model, $rootNamespace);

        if (! class_exists($model)) {
            $this->call('generator:make:model', [
                'name' => $model,
                '--namespace' => $rootNamespace,
                '--path' => $relativePath . '/src/Models'
            ]);
        }

        $this->call('generator:make:controller', [
            'name' => studly_case(class_basename($model)) . 'Controller',
            '--namespace' => $rootNamespace,
            '--path' => $relativePath . '/src/Http/Controllers',
            '--model' => $model,
            '--skipCheckModel' => true,
            '--view' => $this->getViewNamespace($rootNamespace) . '::'
        ]);

        $this->call('generator:make:view', [
            'name' => class_basename($model),
            '--path' => $relativePath . '/src/resources/views',
            '--view' => $this->getViewNamespace($rootNamespace) . '::'
        ]);
    }

    /**
     * Creates a route file.
     *
     * @param string $packagePath The package path
     * @param string $model The model
     * @param string $rootNamespace The root namespace
     * @return void
     */
    private function createRouteFile($packagePath, $model, $rootNamespace)
    {
        $filePath = $packagePath . '/src/routes.php';
        if ($this->files->exists($filePath)) {
            return;
        }

        $stubPath = $this->getTemplatePath() . '/package/routes.stub';
        $stub = $this->files->get($stubPath);

        $modelClass = class_basename($model);
        $replaces = [
            'DummyNamespaceController' => $rootNamespace . '\\Http\\Controllers',
            'DummyResourceUrl' => str_plural(snake_case($modelClass)),
            'DummyController' => studly_case($modelClass) . 'Controller',
        ];
        $stub = str_replace(array_keys($replaces), array_values($replaces), $stub);

        $this->files->put($filePath, $stub);

        $this->info('routes.php created successfully.');
    }

    /**
     * Get the view namespace.
     *
     * @param string $rootNamespace The root namespace
     * @return string
     */
    private function getViewNamespace($rootNamespace)
    {
        return collect(explode('\\', $rootNamespace))
            ->map(function ($name) {
                return snake_case($name);
            })
            ->flatten()
            ->implode('.');
    }
}
