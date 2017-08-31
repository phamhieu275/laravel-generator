<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;
use Symfony\Component\Console\Input\InputOption;

use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\GeneratorCommandTrait;

class ProviderGeneratorCommand extends ProviderMakeCommand
{
    use TemplateTrait;
    use GeneratorCommandTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:provider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();

        if ($this->option('model')) {
            return $templatePath . '/package/provider.model.stub';
        }

        return $templatePath . '/provider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        if ($this->option('package')) {
            return $rootNamespace;
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if ($this->option('rootNamespace')) {
            return $this->option('rootNamespace');
        }

        return parent::rootNamespace();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->option('package') && $this->option('model')) {
            $replaces = [
                'DummyControllerNamespace' => $this->rootNamespace() . '\Http\Controllers',
                'DummyPackage' => $this->getPackageViewNamespace($this->option('package')),
            ];

            return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
        }

        return parent::buildClass($name);
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

            ['rootNamespace', null, InputOption::VALUE_OPTIONAL, 'The root namespace of the model'],

            ['package', null, InputOption::VALUE_OPTIONAL, 'The package name which the model is created'],

            ['model', null, InputOption::VALUE_NONE, 'Generate the provider for the given model'],
        ]);
    }
}
