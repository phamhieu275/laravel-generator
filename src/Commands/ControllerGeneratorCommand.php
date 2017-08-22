<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\CommandTrait;

class ControllerGeneratorCommand extends ControllerMakeCommand
{
    use TemplateTrait;
    use CommandTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:controller
        {name : The name of the controller (with the Controller suffix)}
        {--f|force : Force overwriting existing files}
        {--m|model= : Generate a resource controller for the given model}
        {--r|resource= : Generate a resource controller class}
        {--p|parent= : Generate a nested resource controller class}
        {--pk|package= : The package name to generator into}
        {--path= : The relative path the controller is generated}
        {--rns|rootNamespace= : The root namespace of controller class}
        {--pr|prefix= : The namespace/routing prefix to use}
    ';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();

        if ($this->option('parent')) {
            return $templatePath . '/controller.nested.stub';
        } elseif ($this->option('model')) {
            return $templatePath . '/controller.model.stub';
        } elseif ($this->option('resource')) {
            return $templatePath . '/controller.stub';
        }

        return $templatePath . '/controller.plain.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('rootNamespace')) {
            return trim($this->option('rootNamespace'), '\\') . '\Http\Controllers';
        }

        return config('generator.namespace.controller');
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if ($this->option('rootNamespace')) {
            return trim($this->option('rootNamespace'), '\\') . '\\';
        }

        return parent::rootNamespace();
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildModelReplacements(array $replace)
    {
        $replace = parent::buildModelReplacements($replace);

        $model = class_basename($this->option('model'));

        return array_merge($replace, [
            'DummyPaginator'=> str_plural(lcfirst($model)),
            'DummyViewNamespace' => $this->getViewNamespace($model, $this->option('package')),
            'DummyRoutePrefix' => $this->getRoutePrefix($model, $this->option('prefix')),
        ]);
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

        if ($this->option('rootNamespace')) {
            $rootNamespace = $this->option('rootNamespace');
        } else {
            $rootNamespace = config('generator.namespace.model');
        }
        $rootNamespace = trim($rootNamespace, '\\') . '\\';

        if (class_exists($model)) {
            return $model;
        }

        if (! starts_with($model, $rootNamespace)) {
            $model = $rootNamespace . $model;
        }

        return $model;
    }
}
