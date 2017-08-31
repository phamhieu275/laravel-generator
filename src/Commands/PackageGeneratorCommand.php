<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Illuminate\Filesystem\Filesystem;
use Bluecode\Generator\Traits\ManipulatesPackageTrait;
use Bluecode\Generator\Traits\InteractsWithUserTrait;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\ActionViewTrait;

class PackageGeneratorCommand extends Command
{
    use ManipulatesPackageTrait;
    use InteractsWithUserTrait;
    use TemplateTrait;
    use ActionViewTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'gen:package
        {vendor : The vendor part of the namespace}
        {package : The name of package for the namespace}
        {--i|interactive : Interactive mode}
        {--f|force : Force overwriting existing files}
        {--p|path= : The location where the package should be created}
        {--m|model= : The model class name}
        {--a|actions= : The comma-separated action list}
    ';

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

        if (! $this->option('no-interaction')) {
            $this->info('Running composer dump-autoload. Please wait a minute.');
            $this->composer->dumpAutoloads();
        }

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
            return trim($this->option('path'));
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
        return config('generator.path.package') . '/' . $relativePath;
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

        $this->generateProvider($rootNamespace, $packageName, $relativePath);
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
            $this->info('composer.json already exists!');
            return;
        }

        $stubPath = $this->getTemplatePath() . '/package/composer.stub';
        $stub = str_replace('DummyPackageName', $packageName, $this->files->get($stubPath));

        $this->files->put($filePath, $stub);

        $this->info('composer.json created successfully.');
    }

    /**
     * Create the provider class
     *
     * @param string $rootNamespace The root namespace
     * @param string $packageName The package name
     * @param string $relativePath The relative path
     * @return void
     */
    private function generateProvider($rootNamespace, $packageName, $relativePath)
    {
        $arguments = [
            'name' => studly_case($packageName) . 'PackageProvider',
            '--rootNamespace' => $rootNamespace,
            '--path' => "{$relativePath}/src",
            '--package' => $packageName
        ];

        if ($this->option('model')) {
            $arguments['--model'] = true;
        }

        $this->call('gen:provider', $arguments);
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
                '--rootNamespace' => $rootNamespace,
                '--namespace' => $rootNamespace . '\Models',
                '--path' => $relativePath . '/src/Models',
                '--package' => $packageName
            ]);
        }

        $this->call('gen:controller', [
            'name' => $this->getControllerName($modelName),
            '--rootNamespace' => $rootNamespace,
            '--namespace' => $rootNamespace . '\Http\Controllers',
            '--path' => $relativePath . '/src/Http/Controllers',
            '--model' => $modelClass,
            '--package' => $packageName,
            '--routePrefix' => $packageName
        ]);

        $viewFolderName = $this->getViewNamespace($modelName);

        foreach ($this->getListView($this->option('actions')) as $view) {
            $this->call('gen:view', [
                'name' => $view,
                'model' => $modelName,
                '--path' =>  "{$relativePath}/src/resources/views/{$viewFolderName}",
                '--package' => $packageName
            ]);
        }
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
            'DummyPackage' => studly_case(strtolower($packageName))
        ];
        $stub = str_replace(array_keys($replaces), array_values($replaces), $stub);

        if (! $this->files->exists($filePath)) {
            $this->files->put($filePath, $stub);

            $this->info('routes.php created successfully.');
            return;
        }

        $stub = str_replace("<?php\n", '', $stub);
        $content = $this->files->get($filePath);

        if (strpos($content, $stub) !== false) {
            return;
        }

        $this->files->append($filePath, $stub);

        $this->info('routes.php updated successfully');
    }
}
