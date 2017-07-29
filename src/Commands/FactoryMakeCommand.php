<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;

class FactoryMakeCommand extends GeneratorCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'generator:make:factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create factory setup for given table.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Factory';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();

        return $templatePath . '/factory.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return config('generator.namespace_model', $rootNamespace);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return config('generator.path_factory') . $this->argument('name') . 'Factory.php';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['table', 't', InputOption::VALUE_OPTIONAL, 'Indicates if the model is created from the existed table.'],
        ]);
    }
}
