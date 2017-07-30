<?php

namespace Bluecode\Generator\Parser;

use DB;

class SchemaParser
{
    /**
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    protected $schema;

    /**
     * @var FieldParser
     */
    protected $fieldParser;

    /**
     * @var ForeignKeyParser
     */
    protected $foreignKeyParser;

    /**
     * @var string
     */
    protected $database;

    /**
     * A list guard columns
     *
     * @var array
     */
    private $guardFields = [
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token'
    ];

    /**
     * Inital new instance
     *
     * @param string $database
     * @param bool   $ignoreIndexNames
     * @param bool   $ignoreForeignKeyNames
     * @return void
     */
    public function __construct()
    {
        $connection = DB::getDoctrineConnection();
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('bit', 'boolean');

        $this->database = $connection->getDatabase();

        $this->schema = $connection->getSchemaManager();
        $this->fieldParser = new FieldParser();
        $this->foreignKeyParser = new ForeignKeyParser();
    }

    /**
     * Gets the tables.
     *
     * @return mixed
     */
    public function getTables()
    {
        return $this->schema->listTableNames();
    }

    /**
     * Gets the fields.
     *
     * @param string $table The table
     * @return string The fields.
     */
    public function getFields($table)
    {
        return $this->fieldParser->generate($table, $this->schema, $this->database);
    }

    /**
     * Gets the foreign key constraints.
     *
     * @param string $table The table
     * @return string The foreign key constraints.
     */
    public function getForeignKeyConstraints($table)
    {
        return $this->foreignKeyParser->generate($table, $this->schema);
    }

    /**
     * Gets the fillable fields.
     *
     * @param string $table The table
     * @return string The fillable fields.
     */
    public function getFillableFields($table)
    {
        $columns = $this->schema->listTableColumns($table);
        return collect($columns)
            ->filter(function ($column) {
                return ! $column->getAutoincrement() && ! in_array($column->getName(), $this->guardFields);
            });
    }

    /**
     * Determines if it has soft delete.
     *
     * @param string $table The table
     * @return boolean True if has soft delete, False otherwise.
     */
    public function hasSoftDelete($table)
    {
        $schema = $this->schema->listTableColumns($table);
        return isset($schema['deleted_at']) && $schema['deleted_at']->getType()->getName() === 'datetime';
    }

    /**
     * Determines if exist.
     *
     * @param string $table The table
     * @return boolean True if exist, False otherwise.
     */
    public function isExist($table)
    {
        return $this->schema->tablesExist([$table]);
    }
}
