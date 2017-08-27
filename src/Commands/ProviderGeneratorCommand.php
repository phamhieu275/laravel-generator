<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;

class ProviderGeneratorCommand extends ProviderMakeCommand
{
    use TemplateTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:provider
        {name : The name of the provider}
        {--p|path= : The path to generate into}
        {--rns|rootNamespace= : The root namespace of the provider}
        {--pk|package= : The name of the package}
    ';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();

        if ($this->option('package')) {
            return $templatePath . '/package/provider.stub';
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
        if ($this->option('package')) {
            $replaces = [
                'DummyControllerNamespace' => $this->rootNamespace() . '\\Http\\Controllers',
                'DummyPackage' => $this->option('package'),
            ];

            return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
        }

        return parent::buildClass($name);
    }
}
