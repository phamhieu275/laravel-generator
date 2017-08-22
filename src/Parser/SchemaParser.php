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
     * A list guard columns
     *
     * @var array
     */
    private $guardFields = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
        'remember_token'
    ];

    /**
     * Inital new instance
     *
     * @param ColumnParser $columnParser The column parser
     * @return object
     */
    public function __construct(ColumnParser $columnParser)
    {
        $this->columnParser = $columnParser;
    }

    /**
     * init the connection before call the method
     *
     * @param string $method The method name
     * @param array $arguments The arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            $this->initConnection();
            return call_user_func_array([$this, $method], $arguments);
        }
    }

    /**
     * initial the connection to the database
     */
    protected function initConnection()
    {
        if (! $this->schema) {
            $this->schema = DB::getDoctrineSchemaManager();
        }
    }

    /**
     * Gets the tables.
     *
     * @return mixed
     */
    protected function getTables()
    {
        return $this->schema->listTableNames();
    }

    protected function tablesExist($table)
    {
        return $this->schema->tablesExist($table);
    }

    /**
     * Gets the fields.
     *
     * @param string $table The table
     * @return string The fields.
     */
    protected function getColumns($table)
    {
        return $this->columnParser->parse($this->schema->listTableColumns($table));
    }

    /**
     * Gets the fillable fields.
     *
     * @param string $table The table
     * @return string The fillable fields.
     */
    protected function getFillableColumns($table)
    {
        $columns = $this->schema->listTableColumns($table);
        return collect($columns)
            ->filter(function ($column) {
                return ! $column->getAutoincrement() && ! in_array($column->getName(), $this->guardFields);
            });
    }

    /**
     * Determine whether the table uses soft delete.
     *
     * @param string $table The table
     * @return boolean True if has soft delete, False otherwise.
     */
    protected function hasSoftDelete($table)
    {
        $schema = $this->schema->listTableColumns($table);
        return isset($schema['deleted_at']) && $schema['deleted_at']->getType()->getName() === 'datetime';
    }

    /**
     * Determine whether the table is exist.
     *
     * @param string $table The table
     * @return boolean True if exist, False otherwise.
     */
    protected function isExist($table)
    {
        return $this->schema->tablesExist([$table]);
    }
}
