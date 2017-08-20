<?php

namespace Bluecode\Generator\Parser;

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
     * Create array of all the fields for a table
     *
     * @param string                                      $table Table Name
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     *
     * @return array|bool
     */
    public function generate($table, $schema)
    {
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
        $fields = [];
        foreach ($columns as $column) {
            $name = $column->getName();
            $type = $column->getType()->getName();
            $length = $column->getLength();
            $default = $column->getDefault();
            $nullable = (!$column->getNotNull());
            $index = $indexParser->getIndex($name);

            $decorators = null;
            $args = null;

            if (isset($this->fieldTypeMap[$type])) {
                $type = $this->fieldTypeMap[$type];
            }

            // Different rules for different type groups
            if (in_array($type, ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger'])) {
                // Integer
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
            } elseif ($type == 'dateTime') {
                if ($name == 'deleted_at' && $nullable) {
                    $nullable = false;
                    $type = 'softDeletes';
                    $name = '';
                } elseif ($name == 'created_at' && isset($fields['updated_at'])) {
                    $fields['updated_at'] = ['field' => '', 'type' => 'timestamps'];
                    continue;
                } elseif ($name == 'updated_at' and isset($fields['created_at'])) {
                    $fields['created_at'] = ['field' => '', 'type' => 'timestamps'];
                    continue;
                }
            } elseif (in_array($type, ['decimal', 'float', 'double'])) {
                // Precision based numbers
                $args = $this->getPrecision($column->getPrecision(), $column->getScale());
                if ($column->getUnsigned()) {
                    $decorators[] = 'unsigned';
                }
            } else {
                // Probably not a number (string/char)
                if ($type === 'string' && $column->getFixed()) {
                    $type = 'char';
                }
                $args = $this->getLength($length);
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
            if ($decorators) {
                $field['decorators'] = $decorators;
            }
            if ($args) {
                $field['args'] = $args;
            }
            $fields[$name] = $field;
        }
        return $fields;
    }

    /**
     * Get the length of a field
     *
     * @param int $length
     * @return int|null
     */
    protected function getLength($length)
    {
        if ($length and $length !== 255) {
            return $length;
        }

        return null;
    }

    /**
     * Ge the default value of a field
     *
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
        } elseif (in_array($type, ['string', 'text']) or !is_numeric($default)) {
            $default = $this->argsToString($default);
        }
        return $this->decorate('default', $default, '');
    }

    /**
     * Get the precision value
     *
     * @param int $precision
     * @param int $scale
     * @return string|void
     */
    protected function getPrecision($precision, $scale)
    {
        if ($precision != 8 or $scale != 2) {
            $result = $precision;
            if ($scale != 2) {
                $result .= ', ' . $scale;
            }
            return $result;
        }
    }

    /**
     * Convert arguments to string
     *
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
     *
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
     * Get the information of index
     *
     * @param IndexParser $indexParser
     * @return array
     */
    protected function getMultiFieldIndexes(IndexParser $indexParser)
    {
        $indexes = [];
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
