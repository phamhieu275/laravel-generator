<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;

class ProviderGeneratorCommand extends ProviderMakeCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:provider';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['namespace', '', InputOption::VALUE_OPTIONAL, 'The namespace of provider class'],
            ['path', '', InputOption::VALUE_OPTIONAL, 'The location where the provider file should be created.'],
            ['model', '', InputOption::VALUE_OPTIONAL, 'Indicates if the model is used to generate crud'],
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

        if ($this->option('model')) {
            return $templatePath . '/provider.model.stub';
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
        if ($this->option('namespace')) {
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
        if ($this->option('model') && $this->option('view')) {
            $replaces = [
                'DummyControllerNamespace' => $this->rootNamespace() . '\\Http\\Controllers',
                'DummyViewNamespace' => trim($this->option('view')),
            ];

            return str_replace(array_keys($replaces), array_values($replaces), parent::buildClass($name));
        }

        return parent::buildClass($name);
    }
}
