<?php

namespace Bluecode\Generator\Syntax;

class TableSyntax
{
    /**
     * Get the define table to create the up migrate
     *
     * @param array $fields
     * @return array
     */
    public function getDefineTable($columns)
    {
        $padding = str_repeat(' ', 12);

        $define = '';
        foreach ($columns as $column) {
            $define .= $padding . $this->getDefineColumn($column) . PHP_EOL;
        }

        return $define;
    }

    /**
     * Define a column
     *
     * @param array $field
     * @return string
     */
    protected function getDefineColumn($column)
    {
        $define = '';
        foreach ($column as $property) {
            $func = array_shift($property);
            $args = implode(', ', $property);

            $define .= sprintf('->%s(%s)', $func, $args);
        }

        return "\$table{$define};";
    }
}
