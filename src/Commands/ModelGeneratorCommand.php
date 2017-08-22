<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\CommandTrait;

class ModelGeneratorCommand extends ModelMakeCommand
{
    use TemplateTrait;
    use CommandTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:model
        {name : The name of the model}
        {--a|all : Generate a migration, factory, and resource controller for the model}
        {--c|controller : Create a new controller for the model}
        {--fa|factory : Create a new factory for the model}
        {--f|force : Force overwriting existing files}
        {--m|migration : Create a new migration file for the model}
        {--r|resource : Indicates if the generated controller should be a resource controller}
        {--d|softDelete : Indicates if the model uses the soft delete trait}
        {--t|table= : The table name for the model}
        {--rns|rootNamespace= : The root namespace of model class}
        {--p|path= : The location where the model file should be created}
    ';

    /**
     * Create a new controller creator command instance.
     *
     * @param \Bluecode\Generator\Parser\SchemaParser $schemaParser The schema parser
     * @return void
     */
    public function __construct(Filesystem $files, SchemaParser $schemaParser)
    {
        parent::__construct($files);

        $this->schemaParser = $schemaParser;
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        $this->call('gen:migration', [
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call('gen:controller', [
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') ? $modelName : null,
            '--force' => $this->option('force')
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
        if ($this->option('rootNamespace')) {
            return trim($this->option('rootNamespace'), '\\') . '\Models';
        }

        return config('generator.namespace.model');
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
     * Get the table name.
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
            $this->tableName = str_plural(snake_case(class_basename($this->argument('name'))));
        }

        return $this->tableName;
    }

    /**
     * Get fillable fields from database schema
     *
     * @param string $tableName The table name
     * @return string The fillable.
     */
    protected function getFillable($tableName)
    {
        $fields = $this->schemaParser->getFillableColumns($tableName);

        return $fields
            ->map(function ($field) {
                return "'{$field->getName()}'";
            })
            ->flatten()
            ->implode(', ');
    }
}
