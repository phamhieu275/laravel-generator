<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Creator\MigrationCreator;

class AllMigrationGeneratorCommand extends Command
{

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:all:migration
        {--o|only= : Create migration file for only tables}
        {--e|exclude= : The exclude table list}
        {--path= : The location where the migration file should be created.}
    ';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate many migration file.';

    /**
     * Create new console command
     *
     * @param \Bluecode\Generator\Parser\SchemaParser $schemaParser The schema parser
     * @return void
     */
    public function __construct(Composer $composer, SchemaParser $schemaParser, MigrationCreator $creator)
    {
        parent::__construct();
        $this->schema = $schemaParser;
        $this->composer = $composer;
        $this->creator = $creator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = $this->getListTable();
        foreach ($tables as $table) {
            $name = "create_{$table}_table";
            if (class_exists(studly_case($name))) {
                continue;
            }

            $file = pathinfo($this->creator->create($name, $this->getMigrationPath(), $table, true), PATHINFO_FILENAME);

            $this->line("<info>Created Migration:</info> {$file}");
        }

        $this->composer->dumpAutoloads();
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return base_path() . DIRECTORY_SEPARATOR . $targetPath;
        }

        return database_path('migrations');
    }

    /**
     * Get the list table from schema
     *
     * @return array
     */
    private function getListTable()
    {
        if ($this->option('only')) {
            $tables = explode(',', trim($this->option('only')));
        } else {
            $tables = $this->schema->getTables();
        }

        if ($this->option('exclude')) {
            $excludeTables = explode(',', trim($this->option('exclude')));
            $tables = array_diff($tables, $excludeTables);
        }

        return $tables;
    }
}
