<?php

namespace Bluecode\Generator\Parser;

class ColumnParser
{

    /**
     * Parse the information of the column
     *
     * @param \Doctrine\DBAL\Schema\Column[] $columns
     * @return array
     */
    public function parse($columns)
    {
        $fields = [];
        foreach ($columns as $name => &$column) {
            $field = [];

            $type = $column->getType()->getName();
            switch ($type) {
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'integer':
                case 'bigint':
                    $field[] = $this->parseIntegerColumn($type, $column);
                    break;
                case 'datetime':
                    $field[] = ['dateTime', $this->addQuote($name)];
                    break;
                case 'decimal':
                    $field[] = $this->parseDecimalColumn($type, $column);
                    break;
                case 'float':
                case 'double':
                    $field[] = $this->parseFloatColumn($type, $column);
                    break;
                case 'string':
                    $field[] = $this->parseStringColumn($type, $column);
                    break;
                case 'blob':
                    $field[] = ['binary', $this->addQuote($name)];
                    break;
                default:
                    $field[] = [$type, $this->addQuote($name)];
            }

            if ($this->isNullableColumn($column)) {
                $field[] = ['nullable'];
            }

            if (! is_null($column->getDefault())) {
                $field[] = $this->getDefaultValue($column);
            }

            if (! is_null($column->getComment())) {
                $field[] = ['comment', $this->addQuote($column->getComment())];
            }

            $fields[$name] = $field;
        }

        if ($this->hasSpecifyTimestampColumn($columns, 'deleted_at')) {
            unset($fields['deleted_at']);
            $fields['softDeletes'] = [['softDeletes']];
        }

        if ($this->hasSpecifyTimestampColumn($columns, 'created_at')
            && $this->hasSpecifyTimestampColumn($columns, 'updated_at')
        ) {
            unset($fields['created_at']);
            unset($fields['updated_at']);
            $fields['timestamps'] = [['timestamps']];
        }

        return $fields;
    }

    /**
     * Get the information of the integer column
     *
     * @param string $type The type
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return array
     */
    private function parseIntegerColumn($type, $column)
    {
        if ($type !== 'integer') {
            $type = str_replace('int', 'Integer', $type);
        }

        $hasIncrement = $column->getAutoincrement();
        $hasUnsigned = $column->getUnsigned();

        $name = $this->addQuote($column->getName());

        // only auto-increment
        if ($hasIncrement && ! $hasUnsigned) {
            return [$type, $name, 'true'];
        }

        // both is true
        if ($hasIncrement && $hasUnsigned) {
            $type = lcfirst(str_replace('Integer', 'Increments', ucfirst($type)));
        }

        // only unsigned
        if (! $hasIncrement && $hasUnsigned) {
            $type = 'unsigned' . ucfirst($type);
        }

        return [$type, $name];
    }

    /**
     * Get the information of the decimal column
     *
     * @param string $type The type
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return array
     */
    private function parseDecimalColumn($type, $column)
    {
        if ($column->getUnsigned()) {
            $type = 'unsignedDecimal';
        }

        return [
            $type,
            $this->addQuote($column->getName()),
            $column->getPrecision(),
            $column->getScale()
        ];
    }

    /**
     * Get the information of the float column
     *
     * @param string $type The type
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return array
     */
    private function parseFloatColumn($type, $column)
    {
        return [
            $type,
            $this->addQuote($column->getName()),
            $column->getPrecision(),
            $column->getScale()
        ];
    }

    /**
     * Get the information of the string column
     *
     * @param string $type The type
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return array
     */
    private function parseStringColumn($type, $column)
    {
        if ($column->getFixed()) {
            $type = 'char';
        }

        return [
            $type,
            $this->addQuote($column->getName()),
            $column->getLength()
        ];
    }

    /**
     * Get the default value
     *
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return array
     */
    private function getDefaultValue($column)
    {
        $defaultValue = $column->getDefault();
        $type = $column->getType()->getName();

        if ($type === 'timestamp' && $defaultValue === 'CURRENT_TIMESTAMP') {
            return ['default', "DB::raw('CURRENT_TIMESTAMP')"];
        }

        if (strpos($type, 'int') === false && $type !== 'boolean') {
            $defaultValue = $this->addQuote($defaultValue);
        }

        return ['default', $defaultValue];
    }

    /**
     * Add a quote to the input string
     *
     * @param string $str
     * @return string
     */
    private function addQuote($str)
    {
        return sprintf("'%s'", $str);
    }

    /**
     * Determines if nullable column
     *
     * @param \Doctrine\DBAL\Schema\Column $column The column
     * @return boolean True if nullable column, False otherwise.
     */
    private function isNullableColumn($column)
    {
        return ! $column->getNotNull();
    }

    /**
     * Determines if it has specify timestamp column
     *
     * @param array $columns The columns
     * @param string $columnName The column name
     * @return boolean True if has specify timestamp column, False otherwise.
     */
    private function hasSpecifyTimestampColumn($columns, $columnName)
    {
        if (! isset($columns[$columnName])) {
            return false;
        }

        $type = $columns[$columnName]->getType()->getName();
        return $type === 'timestamp' && $this->isNullableColumn($columns[$columnName]);
    }
}
