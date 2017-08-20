<?php

namespace Bluecode\Generator\Parser;

class IndexParser
{

    /**
     * @var array
     */
    protected $indexes;

    /**
     * @var array
     */
    protected $multiFieldIndexes;

    /**
     * Initial new instance
     *
     * @param string                                      $table Table Name
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     * @param bool                                        $ignoreIndexNames
     */
    public function __construct($table, $schema)
    {
        $this->indexes = [];
        $this->multiFieldIndexes = [];

        $indexes = $schema->listTableIndexes($table);

        foreach ($indexes as $index) {
            $indexArray = $this->indexToArray($table, $index);
            if (count($indexArray['columns']) == 1) {
                $columnName = $indexArray['columns'][0];
                $this->indexes[ $columnName ] = (object) $indexArray;
            } else {
                $this->multiFieldIndexes[] = (object) $indexArray;
            }
        }
    }


    /**
     * convert the index to array
     *
     * @param string $table
     * @param \Doctrine\DBAL\Schema\Index $index
     * @return array
     */
    protected function indexToArray($table, $index)
    {
        if ($index->isPrimary()) {
            $type = 'primary';
        } elseif ($index->isUnique()) {
            $type = 'unique';
        } else {
            $type = 'index';
        }
        $array = [
            'type' => $type,
            'name' => null,
            'columns' => $index->getColumns()
        ];

        if (!$this->isDefaultIndexName($table, $index->getName(), $type, $index->getColumns())) {
            $array['name'] = $index->getName();
        }
        return $array;
    }

    /**
     * @param string $table Table Name
     * @param string $type Index Type
     * @param string|array $columns Column Names
     * @return string
     */
    protected function getDefaultIndexName($table, $type, $columns)
    {
        if ($type == 'primary') {
            return 'PRIMARY';
        }
        if (is_array($columns)) {
            $columns = implode('_', $columns);
        }
        return $table . '_' . $columns . '_' . $type;
    }

    /**
     * Determines if default index name.
     *
     * @param string $table Table Name
     * @param string $name Current Name
     * @param string $type Index Type
     * @param string|array $columns Column Names
     * @return bool
     */
    protected function isDefaultIndexName($table, $name, $type, $columns)
    {
        return $name == $this->getDefaultIndexName($table, $type, $columns);
    }


    /**
     * Gets the index.
     *
     * @param string $name
     * @return null|object
     */
    public function getIndex($name)
    {
        if (isset($this->indexes[$name])) {
            return (object) $this->indexes[$name];
        }
        return null;
    }

    /**
     * Gets the multi field indexes.
     *
     * @return null|object
     */
    public function getMultiFieldIndexes()
    {
        return $this->multiFieldIndexes;
    }
}
