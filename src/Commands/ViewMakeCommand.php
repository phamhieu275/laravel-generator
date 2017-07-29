<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;

class ViewMakeCommand extends GeneratorCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'generator:make:view';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['path', '', InputOption::VALUE_OPTIONAL, 'The location where the view file should be created.'],
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();
        return $templatePath . '/views/';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $name = $this->argument('name');
        $viewPath = $this->getPath($name);
        if (! $this->files->isDirectory($viewPath)) {
            $this->files->makeDirectory($viewPath, 0777, true, true);
        }

        $templatePath = $this->getStub();

        $actions = ['index', 'create', 'edit', 'show'];
        foreach ($actions as $action) {
            $stub = $this->files->get($templatePath . "{$action}.blade.stub");

            $method = 'build'.ucfirst($action);
            if (method_exists($this, $method)) {
                $stub = $this->{$method}($stub, $name);
            }

            $path = $viewPath . "/{$action}.blade.php";
            $this->files->put($path, $stub);

            $this->info("{$action}.blade.php is created successfully.");
        }
    }

    /**
     * Get the destination class path.
     *
     * @param string $name
     * @return string
     */
    protected function getPath($name)
    {
        if ($this->option('path')) {
            $basePath = trim($this->option('path'));
        } else {
            $basePath = config('generator.path_view');
        }

        return trim($basePath, '/') . '/' . str_plural(snake_case($name));
    }

    /**
     * Builds an index.
     *
     * @param string $stub The stub
     * @param string $name The name
     * @return string The index.
     */
    private function buildIndex($stub, $name)
    {
        $placeholders = [
            'DummyMainLayout',
            'DummyResourceUrl',
            'DummyModelPluralVariable',
            'DummyModelVariable',
        ];

        $replaces = [
            config('generator.main_layout'),
            str_plural(snake_case($name)),
            str_plural(lcfirst($name)),
            strtolower($name),
        ];

        return str_replace($placeholders, $replaces, $stub);
    }

    /**
     * Builds a create.
     *
     * @param string $stub The stub
     * @param string $name The name
     * @return string The create.
     */
    private function buildCreate($stub, $name)
    {
        $placeholders = [
            'DummyMainLayout',
            'DummyResourceUrl',
            'DummyModelVariable',
        ];

        $replaces = [
            config('generator.main_layout'),
            str_plural(snake_case($name)),
            strtolower($name),
        ];

        return str_replace($placeholders, $replaces, $stub);
    }

    /**
     * Builds an edit.
     *
     * @param string $stub The stub
     * @param string $name The name
     * @return string The edit.
     */
    private function buildEdit($stub, $name)
    {
        $placeholders = [
            'DummyMainLayout',
            'DummyResourceUrl',
            'DummyModelVariable',
        ];

        $replaces = [
            config('generator.main_layout'),
            str_plural(snake_case($name)),
            strtolower($name),
        ];

        return str_replace($placeholders, $replaces, $stub);
    }

    /**
     * Builds a show.
     *
     * @param string $stub The stub
     * @param string $name The name
     * @return string The show.
     */
    private function buildShow($stub, $name)
    {
        $placeholders = [
            'DummyMainLayout',
            'DummyModelVariable',
        ];

        $replaces = [
            config('generator.main_layout'),
            strtolower($name),
        ];

        return str_replace($placeholders, $replaces, $stub);
    }
}
