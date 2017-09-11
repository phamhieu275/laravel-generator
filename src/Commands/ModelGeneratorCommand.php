<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;

use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\GeneratorCommandTrait;

class ModelGeneratorCommand extends ModelMakeCommand
{
    use TemplateTrait;
    use GeneratorCommandTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:model';

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
        $table = str_plural(snake_case(class_basename($this->argument('name'))));

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
        $controller = studly_case(class_basename($this->argument('name')));

        $modelName = $this->qualifyClass($this->getNameInput());

        $this->call('gen:controller', [
            'name' => "{$controller}Controller",
            '--model' => $this->option('resource') ? $modelName : null,
            '--overwrite' => $this->option('overwrite')
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
        if ($this->option('table')) {
            return trim($this->option('table'));
        }

        return str_plural(snake_case(class_basename($this->argument('name'))));
    }

    /**
     * Get fillable fields from database schema
     *
     * @param string $tableName The table name
     * @return string The fillable.
     */
    protected function getFillable($tableName)
    {
        if ($this->option('fillable')) {
            $fields = collect(explode(',', trim($this->option('fillable'))));
        } else {
            $fields = $this->schemaParser->getFillableColumns($tableName);
        }

        return $fields
            ->map(function ($field) {
                if (is_string($field)) {
                    return "'{$field}'";
                }

                return "'{$field->getName()}'";
            })
            ->flatten()
            ->implode(', ');
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

            ['softDelete', 'd', InputOption::VALUE_NONE, 'Indicates if the model uses the soft delete trait'],

            ['table', 't', InputOption::VALUE_OPTIONAL, 'The table name for the model'],

            ['fillable', null, InputOption::VALUE_OPTIONAL, 'The comma-separated fillable fields for the model'],

            ['namespace', null, InputOption::VALUE_OPTIONAL, 'The namespace of the model'],

            ['rootNamespace', null, InputOption::VALUE_OPTIONAL, 'The root namespace of the model'],

            ['package', null, InputOption::VALUE_OPTIONAL, 'The package name which the model is created'],
        ]);
    }
}
