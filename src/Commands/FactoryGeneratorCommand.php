<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Bluecode\Generator\Traits\TemplateTrait;

class FactoryGeneratorCommand extends FactoryMakeCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:factory
        {name : The name of the factory}
        {--model= : The name of the model}
    ';

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
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(['\\', '/'], '', $this->argument('name'));

        return config('generator.path.factory') . "/{$name}.php";
    }
}
