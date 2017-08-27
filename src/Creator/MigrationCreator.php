<?php

namespace Bluecode\Generator\Creator;

use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Syntax\TableSyntax;

class MigrationCreator extends BaseMigrationCreator
{
    /**
     * The placeholder to replace with the schema of the table
     *
     * @var string
     */
    protected $placeholder = '/(?<=function\s\(Blueprint\s\$table\)\s\{\n)[^\}]*\n/';

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files, SchemaParser $schemaParser, TableSyntax $tableSyntax)
    {
        $this->files = $files;
        $this->schemaParser = $schemaParser;
        $this->tableSyntax = $tableSyntax;
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = parent::populateStub($name, $stub, $table);

        // if the table is existed, update the schema into the migration file
        if ($this->schemaParser->tablesExist($table)) {
            $defineTable = $this->tableSyntax->getDefineTable($this->schemaParser->getTableInformation($table));

            $stub = preg_replace($this->placeholder, $defineTable, $stub);
        }

        return $stub;
    }
}
