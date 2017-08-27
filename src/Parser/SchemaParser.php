<?php

namespace Bluecode\Generator\Parser;

use DB;
use Doctrine\DBAL\Types\Type;

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
     * @param IndexParser $indexParser The index parser
     * @return object
     */
    public function __construct(ColumnParser $columnParser, IndexParser $indexParser)
    {
        $this->columnParser = $columnParser;
        $this->indexParser = $indexParser;
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
            if (! Type::hasType('timestamp')) {
                Type::addType('timestamp', 'Bluecode\Generator\Doctrine\Timestamp');
            }

            $platform = DB::getDoctrineConnection()->getDatabasePlatform();

            if (! $this->hasDoctrineTypeMappingFor('timestamp')) {
                $platform->registerDoctrineTypeMapping('Timestamp', 'timestamp');
            }

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
        $tables = $this->schema->listTableNames();
        if (($key = array_search('migrations', $tables)) !== false) {
            unset($tables[$key]);
        }
        return $tables;
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
    protected function getTableInformation($table)
    {
        $columns = $this->schema->listTableColumns($table);
        $indexes = $this->schema->listTableIndexes($table);

        return array_merge(
            $this->columnParser->parse($columns),
            $this->indexParser->parse($columns, $indexes)
        );
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
}
