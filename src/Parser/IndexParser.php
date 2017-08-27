<?php

namespace Bluecode\Generator\Parser;

class IndexParser
{
    /**
     * Get the information of the index
     *
     * @param array $columns The columns
     * @param array $indexes The indexes
     * @return array
     */
    public function parse($columns, $indexes)
    {
        $flag = $this->hasAutoIncrementColumn($columns);

        $result = [];
        foreach ($indexes as $name => $index) {
            if ($index->isPrimary() && $flag) {
                continue;
            }

            if ($index->isPrimary()) {
                $type = 'primary';
            } elseif ($index->isUnique()) {
                $type = 'unique';
            } else {
                $type = 'index';
            }

            $indexColumns = "['" . implode("', '", $index->getColumns()) . "']";

            if ($type === 'primary') {
                $result[] = [
                    [$type, $indexColumns]
                ];
            } else {
                $result[] = [
                    [$type, $indexColumns, "'" . $name . "'"]
                ];
            }
        }

        return $result;
    }

    /**
     * Determines if it has automatic increment column.
     *
     * @param array $columns The columns
     * @return boolean True if has automatic increment column, False otherwise.
     */
    private function hasAutoIncrementColumn($columns)
    {
        foreach ($columns as $column) {
            if ($column->getAutoincrement()) {
                return true;
            }
        }

        return false;
    }
}
