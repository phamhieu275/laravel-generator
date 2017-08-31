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
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:provider
        {name : The name of the provider}
        {--rns|rootNamespace= : The root namespace of the provider class}
        {--pk|package= : The name of the package}
        {--p|path= : The path position to generate}
        {--m|model : Use provider.model template}
    ';

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
}
