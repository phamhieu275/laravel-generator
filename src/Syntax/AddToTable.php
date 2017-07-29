<?php

namespace Bluecode\Generator\Syntax;

/**
 * Class AddToTable
 */
class AddToTable extends Table
{

    /**
     * Return string for adding a column
     *
     * @param array $field
     * @return string
     */
    protected function getItem($field)
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
}
