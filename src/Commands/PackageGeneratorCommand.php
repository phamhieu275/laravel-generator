<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\ManipulatesPackageTrait;
use Bluecode\Generator\Traits\InteractsWithUserTrait;

class PackageGeneratorCommand extends Command
{
    use TemplateTrait;
    use ManipulatesPackageTrait;
    use InteractsWithUserTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'generator:package
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

        $rootNamespace = $vendorName . '\\' . $packageName;
        $this->generateSkeleton($rootNamespace, $packageName, $relativePath);

        if ($this->option('model')) {
            $this->generateResource($rootNamespace, $packageName, $relativePath, $this->option('model'));
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
        return config('generator.package.base_path') . '/' . $relativePath;
    }

    /**
     * Create skeleton directory, composer.json file and provider file.
     *
     * @param string $rootNamespace The root namespace
     * @param string $packageName The package name
     * @param string $relativePath The relative path
     * @return void
     */
    private function generateSkeleton($rootNamespace, $packageName, $relativePath)
    {
        $packagePath = $this->getPackagePath($relativePath);
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
        $this->call('gen:provider', $providerArguments);
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
     * @param string $rootNamespace The root namespace
     * @param string $packageName The package name
     * @param string $relativePath The relative path
     * @param string $modelClass The model class
     * @return void
     */
    private function generateResource($rootNamespace, $packageName, $relativePath, $modelClass)
    {
        $packagePath = $this->getPackagePath($relativePath);
        $modelName = class_basename($modelClass);

        $this->generateRoute($packagePath, $packageName, $modelName);

        if (! class_exists($modelClass)) {
            $this->call('gen:model', [
                'name' => $modelName,
                '--namespace' => $rootNamespace,
                '--path' => $relativePath . '/src/Models'
            ]);
        }

        $viewNamespace = $this->getViewNamespace($rootNamespace) . '::';

        $this->call('gen:controller', [
            'name' => $this->getControllerName($modelName),
            '--namespace' => $rootNamespace,
            '--path' => $relativePath . '/src/Http/Controllers',
            '--model' => $modelClass,
            '--skipCheckModel' => true,
            '--view' =>  $viewNamespace,
            '--package' => $packageName
        ]);

        $this->call('gen:view', [
            'name' => $modelName,
            '--path' => $relativePath . '/src/resources/views',
            '--view' => $viewNamespace,
            '--package' => $packageName
        ]);
    }

    /**
     * Define routes for controller actions.
     *
     * @param string $packagePath The package path
     * @param string $packageName The package name
     * @param string $model The model name
     * @return void
     */
    private function generateRoute($packagePath, $packageName, $modelName)
    {
        $filePath = $packagePath . '/src/routes.php';

        $stubPath = $this->getTemplatePath() . '/package/routes.stub';
        $stub = $this->files->get($stubPath);

        $replaces = [
            'DummyRoutePrefix' => $this->getRoutePrefix($modelName),
            'DummyController' => $this->getControllerName($modelName),
            'DummyPackageName' => snake_case($packageName)
        ];
        $stub = str_replace(array_keys($replaces), array_values($replaces), $stub);

        if ($this->files->exists($filePath)) {
            $stub = str_replace('<?php', '', $stub);

            $this->files->append($filePath, $stub);

            $this->info('routes.php updated successfully');
        } else {
            $this->files->put($filePath, $stub);

            $this->info('routes.php created successfully.');
        }
    }
}
