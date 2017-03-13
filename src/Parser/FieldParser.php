<?php namespace Bluecode\Generator\Parser;

class FieldParser
{

    /**
     * Convert dbal types to Laravel Migration Types
     * @var array
     */
    protected $fieldTypeMap = [
        'tinyint'  => 'tinyInteger',
        'smallint' => 'smallInteger',
        'mediumint'=> 'mediumInteger',
        'bigint'   => 'bigInteger',
        'datetime' => 'dateTime',
        'blob'     => 'binary',
    ];

    /**
     * @var string
     */
    protected $database;

    /**
     * Create array of all the fields for a table
     *
     * @param string                                      $table Table Name
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     * @param string                                      $database
     *
     * @return array|bool
     */
    public function generate($table, $schema, $database)
    {
        $this->database = $database;
        $columns = $schema->listTableColumns($table);
        if (empty($columns)) {
            return false;
        }

        $indexParser = new IndexParser($table, $schema);
        $fields = $this->getFields($columns, $indexParser);
        $indexes = $this->getMultiFieldIndexes($indexParser);
        return array_merge($fields, $indexes);
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column[] $columns
     * @param IndexParser $indexParser
     * @return array
     */
    protected function getFields($columns, IndexParser $indexParser)
    {
        $hasFieldCreatedAt = $hasFieldUpdatedAt = false;
        $fields = array();
        foreach ($columns as $column) {
            $name = $column->getName();
            $type = $column->getType()->getName();
            $length = $column->getLength();
            $default = $column->getDefault();
            $nullable = (!$column->getNotNull());
            $index = $indexParser->getIndex($name);

            $decorators = [];
            $args = null;

            if (isset($this->fieldTypeMap[$type])) {
                $type = $this->fieldTypeMap[$type];
            }

            switch ($type) {
                case 'tinyInteger':
                case 'smallInteger':
                case 'mediumInteger':
                case 'integer':
                case 'bigInteger':
                    if ($column->getUnsigned() && $column->getAutoincrement()) {
                        if ($type == 'integer') {
                            $type = 'increments';
                        } else {
                            $type = str_replace('Integer', 'Increments', $type);
                        }

                        $index = null;
                    } else {
                        if ($column->getUnsigned()) {
                            $decorators[] = 'unsigned';
                        }

                        if ($column->getAutoincrement()) {
                            $args = 'true';
                            $index = null;
                        }
                    }
                    break;

                case 'timestamps':
                    if ($name == 'created_at') {
                        $hasFieldCreatedAt = true;
                    }

                    if ($name == 'updated_at') {
                        $hasFieldUpdatedAt = true;
                    }
                    break;

                case 'dateTime':
                    if ($name == 'deleted_at' && $nullable) {
                        $nullable = false;
                        $type = 'softDeletes';
                        $name = '';
                    }
                    break;

                case 'decimal':
                case 'float':
                case 'double':
                    // Precision based numbers
                    $args = $this->getPrecision($column->getPrecision(), $column->getScale());
                    if ($column->getUnsigned()) {
                        $decorators[] = 'unsigned';
                    }
                    break;

                default:
                    // Probably not a number (string/char)
                    if ($type === 'string' && $column->getFixed()) {
                        $type = 'char';
                    }

                    if ($type !== 'text') {
                        $args = $this->getLength($length);
                    }
                    break;
            }

            if ($nullable) {
                $decorators[] = 'nullable';
            }

            if ($default !== null) {
                $decorators[] = $this->getDefault($default, $type);
            }

            if ($index) {
                $decorators[] = $this->decorate($index->type, $index->name);
            }

            $field = ['field' => $name, 'type' => $type];

            $comment = $column->getComment();
            if (!empty($comment)) {
                $decorators[] = $this->decorate('comment', $comment);
            }

            if (!empty($decorators)) {
                $field['decorators'] = $decorators;
            }

            if ($args) {
                $field['args'] = $args;
            }

            $fields[] = $field;
        }

        if ($hasFieldCreatedAt && $hasFieldUpdatedAt) {
            $fields[] = ['field' => '', 'type' => 'timestamps'];
        }

        return $fields;
    }

    /**
     * @param int $length
     * @return int|void
     */
    protected function getLength($length)
    {
        if ($length && $length !== 255) {
            return $length;
        }
    }

    /**
     * @param string $default
     * @param string $type
     * @return string
     */
    protected function getDefault($default, &$type)
    {
        if (in_array($default, ['CURRENT_TIMESTAMP'])) {
            if ($type == 'dateTime') {
                $type = 'timestamp';
            }
            $default = $this->decorate('DB::raw', $default);
        } elseif (in_array($type, ['string', 'text']) || !is_numeric($default)) {
            $default = $this->argsToString($default);
        }
        return $this->decorate('default', $default, '');
    }

    /**
     * @param int $precision
     * @param int $scale
     * @return string|void
     */
    protected function getPrecision($precision, $scale)
    {
        if ($precision != 8 || $scale != 2) {
            $result = $precision;
            if ($scale != 2) {
                $result .= ', ' . $scale;
            }
            return $result;
        }
    }

    /**
     * @param string|array $args
     * @param string       $quotes
     * @return string
     */
    protected function argsToString($args, $quotes = '\'')
    {
        if (is_array($args)) {
            $seperator = $quotes .', '. $quotes;
            $args = implode($seperator, $args);
        }

        return $quotes . $args . $quotes;
    }

    /**
     * Get Decorator
     * @param string       $function
     * @param string|array $args
     * @param string       $quotes
     * @return string
     */
    protected function decorate($function, $args, $quotes = '\'')
    {
        if (! is_null($args)) {
            $args = $this->argsToString($args, $quotes);
            return $function . '(' . $args . ')';
        } else {
            return $function;
        }
    }

    /**
     * @param IndexParser $indexParser
     * @return array
     */
    protected function getMultiFieldIndexes(IndexParser $indexParser)
    {
        $indexes = array();
        foreach ($indexParser->getMultiFieldIndexes() as $index) {
            $indexArray = [
                'field' => $index->columns,
                'type' => $index->type,
            ];
            if ($index->name) {
                $indexArray['args'] = $this->argsToString($index->name);
            }
            $indexes[] = $indexArray;
        }
        return $indexes;
    }
}
