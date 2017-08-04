<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Syntax\TableSyntax;

class MigrationMakeCommand extends BaseCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:migration {table : The table to migrate.}
        {--path= : The location where the migration file should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The placeholder to replace with the schema of the table
     *
     * @var string
     */
    protected $placeholder = '/(?<=function \(Blueprint \$table\) \{\n)[^\}]*\n/';

    /**
     * Create a new migration install command instance.
     *
     * @param \Illuminate\Database\Migrations\MigrationCreator $creator
     * @param \Illuminate\Filesystem\Filesystem $files The files
     * @param \Bluecode\Generator\Parser\SchemaParser $schemaParser The schema parser
     * @param \Bluecode\Generator\Syntax\AddToTable $addToTable The add to table
     * @return void
     */
    public function __construct(MigrationCreator $creator, Filesystem $files, SchemaParser $schemaParser, TableSyntax $tableSyntax)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->files = $files;
        $this->schemaParser = $schemaParser;
        $this->tableSyntax = $tableSyntax;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $table = trim($this->argument('table'));

        $name = 'Create' . studly_case($table);
        $this->writeMigration($name, $table, true);
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string  $name
     * @param  string  $table
     * @param  bool    $create
     * @return string
     */
    protected function writeMigration($name, $table, $create)
    {
        $filePath = $this->creator->create(
            $name,
            $this->getMigrationPath(),
            $table,
            $create
        );

        $fields = $this->schemaParser->getFields($table);

        // if the table is existed, update the schema into the migration file
        if (! empty($fields)) {
            $defineTable = $this->tableSyntax->getDefineTable($fields);

            $content = preg_replace($this->placeholder, $defineTable, $this->files->get($filePath));
            $this->files->put($filePath, $content);
        }

        $file = pathinfo($filePath, PATHINFO_FILENAME);
        $this->info("Created Migration: {$file}");
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath().'/'.$targetPath;
        }

        return parent::getMigrationPath();
    }
}
