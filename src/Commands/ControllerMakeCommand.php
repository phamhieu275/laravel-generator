<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Support\Str;
use Illuminate\Routing\Console\ControllerMakeCommand as BaseControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;

class ControllerMakeCommand extends BaseControllerMakeCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'generator:make:controller';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['namespace', '', InputOption::VALUE_OPTIONAL, 'The root namespace of controller class.'],

            ['path', '', InputOption::VALUE_OPTIONAL, 'The location where the controller file should be created.'],

            ['skipCheckModel', '', InputOption::VALUE_NONE, 'Skip to check whether the model class is exist'],

            ['view', '', InputOption::VALUE_OPTIONAL, 'The view namespace'],
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
        return $templatePath . '/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('namespace')) {
            return trim($this->option('namespace'), '\\') . '\Http\Controller';
        }

        return config('generator.namespace_controller');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if ($this->option('namespace')) {
            return $this->option('namespace');
        }

        return parent::rootNamespace();
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        if ($this->option('path')) {
            return trim($this->option('path'), '/') . '/' . $this->argument('name') . '.php';
        }

        return parent::getPath($name);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $modelClass = $this->parseModel($this->option('model'));

        $placeholders = [
            'DummyModelPluralVariable',
            'DummyViewPath'
        ];

        $replaces = [
            str_plural(lcfirst(class_basename($modelClass))),
            $this->getViewPath($modelClass)
        ];

        return str_replace(
            $placeholders,
            $replaces,
            parent::buildClass($name)
        );
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! $this->option('skipCheckModel') && ! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('generator:make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * get view folder path
     *
     * @param string $modelInput The model class
     * @return string
     */
    protected function getViewPath($modelClass)
    {
        $viewFolder = str_plural(snake_case(class_basename($modelClass)));

        if ($this->option('view')) {
            return $this->option('view') . $viewFolder;
        }

        return $viewFolder;
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if ($this->option('namespace')) {
            $rootNamespace = $this->option('namespace');
        } else {
            $rootNamespace = $this->laravel->getNamespace();
        }

        if (! Str::startsWith($model, $rootNamespace)) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }
}
