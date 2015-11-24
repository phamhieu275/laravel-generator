<?php namespace Bluecode\Generator\Parser;

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
    private $guardFields = ['created_at', 'updated_at', 'deleted_at', 'remember_token'];

    /**
     * @param string $database
     * @param bool   $ignoreIndexNames
     * @param bool   $ignoreForeignKeyNames
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
     * @return mixed
     */
    public function getTables()
    {
        return $this->schema->listTableNames();
    }

    public function getFields($table)
    {
        return $this->fieldParser->generate($table, $this->schema, $this->database);
    }

    public function getForeignKeyConstraints($table)
    {
        return $this->foreignKeyParser->generate($table, $this->schema);
    }

    public function getFillableFieldsFromSchema($schema)
    {
        $fillableFields = [];
        foreach ($schema as $fieldName => $column) {
            if (empty($column['field']) || in_array($column['field'], $this->guardFields)) {
                continue;
            }

            if (in_array($column['type'], [
                'tinyIncrements',
                'smallIncrements',
                'mediumIncrements',
                'increments',
                'bigIncrements'
            ])) {
                continue;
            }

            if (in_array($column['type'], ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger']) &&
                isset($column['args']) &&
                $column['args'] === 'true') {
                continue;
            }
            $fillableFields[$fieldName] = $column;
        }

        return $fillableFields;
    }

    public function getFillableFields($table)
    {
        $schema = $this->getFields($table);
        return $this->getFillableFieldsFromSchema($schema);
    }
}
