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

        if ($this->usePackageProviderTemplate()) {
            return $templatePath . '/package/provider.stub';
        }

        return $templatePath . '/provider.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->usePackageProviderTemplate()) {
            $replaces = [
                'DummyControllerNamespace' => $this->rootNamespace() . '\Http\Controllers',
                'DummyPackage' => $this->getPackageViewNamespace($this->option('package')),
            ];

            return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
        }

        return parent::buildClass($name);
    }

    /**
     * check whether to use package provider template
     *
     * @return boolean
     */
    private function usePackageProviderTemplate()
    {
        return $this->option('package') && ! $this->option('plain');
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

            ['plain', null, InputOption::VALUE_NONE, 'Generate the plain provider'],
        ]);
    }
}
