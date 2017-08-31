<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Symfony\Component\Console\Input\InputOption;

use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\GeneratorCommandTrait;

class ControllerGeneratorCommand extends ControllerMakeCommand
{
    use TemplateTrait;
    use GeneratorCommandTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();

        if ($this->option('model')) {
            return $templatePath . '/controller.model.stub';
        }

        return $templatePath . '/' . basename(parent::getStub());
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
            return trim($this->option('namespace'));
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
            return trim($this->option('rootNamespace'));
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
        $modelClass = $this->parseModel($this->option('model'));

        $model = class_basename($modelClass);

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),

            'DummyPaginator'=> str_plural(lcfirst($model)),
            'DummyViewNamespace' => $this->getViewNamespace($model, $this->option('package')),
            'DummyRoutePrefix' => $this->getRoutePrefix($model, $this->option('routePrefix')),
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

        if (class_exists($model)) {
            return $model;
        }

        if ($this->option('package')) {
            $rootNamespace = trim($this->option('rootNamespace')) . '\Models';
        } else {
            $rootNamespace = config('generator.namespace.model');
        }

        if (! starts_with($model, $rootNamespace)) {
            $model = $rootNamespace . '\\' . $model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['overwrite', null, InputOption::VALUE_NONE, 'Force overwriting existing files'],

            ['path', null, InputOption::VALUE_OPTIONAL, 'The location where the model file should be created'],

            ['namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the model'],

            ['rootNamespace', null, InputOption::VALUE_OPTIONAL, 'The root namespace of the model'],

            ['package', null, InputOption::VALUE_OPTIONAL, 'The package name which the model is created'],

            ['routePrefix', null, InputOption::VALUE_OPTIONAL, 'The prefix route'],
        ]);
    }
}
