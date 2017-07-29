<?php

namespace Bluecode\Generator\Syntax;

/**
 * Class AddForeignKeysToTable
 */
class AddForeignKeysToTable extends Table
{

    /**
     * Return string for adding a foreign key
     *
     * @param array $foreignKey
     * @return string
     */
    protected function getItem($foreignKey)
    {
        $value = $foreignKey['field'];
        if (! empty($foreignKey['name'])) {
            $value .= "', '". $foreignKey['name'];
        }
        $output = sprintf(
            "\$table->foreign('%s')->references('%s')->on('%s')",
            $value,
            $foreignKey['references'],
            $foreignKey['on']
        );
        if ($foreignKey['onUpdate']) {
            $output .= sprintf("->onUpdate('%s')", $foreignKey['onUpdate']);
        }
        if ($foreignKey['onDelete']) {
            $output .= sprintf("->onDelete('%s')", $foreignKey['onDelete']);
        }
        if (isset($foreignKey['decorators'])) {
            $output .= $this->addDecorators($foreignKey['decorators']);
        }
        return $output . ';';
    }
}
