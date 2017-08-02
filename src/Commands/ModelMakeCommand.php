<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as BaseModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Traits\TemplateTrait;

class ModelMakeCommand extends BaseModelMakeCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'generator:make:model';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['softDelete', 'd', InputOption::VALUE_NONE, 'Indicates if the model uses the soft delete trait.'],

            ['table', 't', InputOption::VALUE_OPTIONAL, 'Indicates if the model is created from the existed table.'],

            ['namespace', '', InputOption::VALUE_OPTIONAL, 'The root namespace of model class.'],

            ['path', '', InputOption::VALUE_OPTIONAL, 'The location where the model file should be created.'],
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

        if ($this->option('softDelete')) {
            return $templatePath . '/model.softDelete.stub';
        }

        return $templatePath . '/model.stub';
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
            return $this->option('namespace') . '\Models';
        }

        return config('generator.namespace_model');
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
            return trim($this->option('path'), '/') . '/' . class_basename($name) . '.php';
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
        $stub = parent::buildClass($name);

        $this->replaceTableName($stub)
            ->replaceFillable($stub);

        return $stub;
    }

    /**
     * Replace table name
     *
     * @param string $stub The template content
     * @return string
     */
    protected function replaceTableName(&$stub)
    {
        $tableName = $this->getTableName();

        $stub = str_replace('DummyTableName', $tableName, $stub);

        return $this;
    }

    /**
     * Replace fillable fields
     *
     * @param string $stub The template content
     * @return string
     */
    protected function replaceFillable(&$stub)
    {
        $tableName = $this->getTableName();
        $fillable = $this->getFillable($tableName);

        $stub = str_replace('DummyFillable', $fillable, $stub);

        return $this;
    }

    /**
     * Gets the table name.
     *
     * @return string The table name.
     */
    protected function getTableName()
    {
        if (isset($this->tableName)) {
            return $this->tableName;
        }

        $this->tableName = trim($this->option('table'));

        if (empty($this->tableName)) {
            $this->tableName = str_plural(snake_case($this->argument('name')));
        }

        return $this->tableName;
    }

    /**
     * Gets fillable fields from database schema
     *
     * @param string $tableName The table name
     * @return string The fillable.
     */
    protected function getFillable($tableName)
    {
        $schemaParser = new SchemaParser;
        $fields = $schemaParser->getFillableFields($tableName);

        return $fields
            ->map(function ($field) {
                return "'{$field->getName()}'";
            })
            ->flatten()
            ->implode(', ');
    }
}
