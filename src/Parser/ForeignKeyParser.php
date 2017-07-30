<?php namespace Bluecode\Generator\Parser;

class ForeignKeyParser
{

    /**
     * @var string
     */
    protected $table;

    /**
     * Get array of foreign keys
     *
     * @param string                                      $table Table Name
     * @param \Doctrine\DBAL\Schema\AbstractSchemaManager $schema
     *
     * @return array
     */
    public function generate($table, $schema)
    {
        $this->table = $table;
        $fields = [];

        $foreignKeys = $schema->listTableForeignKeys($table);

        if (empty($foreignKeys)) {
            return array();
        }

        foreach ($foreignKeys as $foreignKey) {
            $fields[] = [
                'name' => $this->getName($foreignKey),
                'field' => $foreignKey->getLocalColumns()[0],
                'references' => $foreignKey->getForeignColumns()[0],
                'on' => $foreignKey->getForeignTableName(),
                'onUpdate' => $foreignKey->hasOption('onUpdate') ? $foreignKey->getOption('onUpdate') : 'RESTRICT',
                'onDelete' => $foreignKey->hasOption('onDelete') ? $foreignKey->getOption('onDelete') : 'RESTRICT',
            ];
        }
        return $fields;
    }

    /**
     * Get the name of foreign key
     *
     * @param      $foreignKey
     * @return null
     */
    private function getName($foreignKey)
    {
        if ($this->isDefaultName($foreignKey)) {
            return null;
        }
        return $foreignKey->getName();
    }

    /**
     * Determines if default name.
     *
     * @param string $foreignKey The foreign key
     * @param $foreignKey
     * @return bool
     */
    private function isDefaultName($foreignKey)
    {
        return $foreignKey->getName() === $this->createIndexName($foreignKey->getLocalColumns()[0]);
    }

    /**
     * Create a default index name for the table.
     *
     * @param  string  $column
     * @return string
     */
    protected function createIndexName($column)
    {
        $index = strtolower($this->table.'_'.$column.'_foreign');

        return str_replace(array('-', '.'), '_', $index);
    }
}
