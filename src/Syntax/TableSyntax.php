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
    protected function getDefineColumn($field)
    {
        $property = $field['field'];

        // If the field is an array,
        // make it an array in the Migration
        if (is_array($property)) {
            $property = "['". implode("','", $property) ."']";
        } else {
            $property = $property ? "'$property'" : null;
        }

        $func = $field['type'];

        // If we have args, then it needs
        // to be formatted a bit differently
        if (isset($field['args'])) {
            $output = sprintf(
                "\$table->%s(%s, %s)",
                $func,
                $property,
                $field['args']
            );
        } else {
            $output = sprintf(
                "\$table->%s(%s)",
                $func,
                $property
            );
        }


        if (isset($field['decorators'])) {
            $output .= $this->addDecorators($field['decorators']);
        }
        return $output . ';';
    }

    /**
     * Add decorators.
     *
     * @param string $decorators The decorators
     * @param $decorators
     * @return string
     */
    protected function addDecorators($decorators)
    {
        $output = '';
        foreach ($decorators as $decorator) {
            $output .= sprintf("->%s", $decorator);
            // Do we need to tack on the parens?
            if (strpos($decorator, '(') === false) {
                $output .= '()';
            }
        }
        return $output;
    }
}
