<?php namespace Bluecode\Generator\Utils;

use DB;

class TableFieldsGenerator
{
    /** @var  string */
    public $tableName;

    /** @var \Doctrine\DBAL\Schema\AbstractSchemaManager  */
    public $schema;

    /** @var \Doctrine\DBAL\Schema\Table  */
    public $table;

    /**
     * unique field variable
     *
     * @var array
     */
    public $uniqueFields = [];

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
        $this->schema = DB::getDoctrineSchemaManager($tableName);
        $this->table = $this->schema->listTableDetails($tableName);

        $this->analyzeIndexes();
    }

    private function analyzeIndexes()
    {
        $indexes = $this->table->getIndexes();

        $this->uniqueFields = [];

        foreach ($indexes as $index) {
            if ($index->isPrimary()) {
                $columns = $index->getColumns();

                if (sizeof($columns) == 1) {
                    $this->primaryKey = $columns[0];
                }
            }

            if ($index->isUnique()) {
                $columns = $index->getColumns();

                if (sizeof($columns) == 1) {
                    $column = $columns[0];
                    if ($column != $this->primaryKey) {
                        $this->uniqueFields[] = $column;
                    }
                }
            }
        }
    }

    public function generateFieldsFromTable($commandType)
    {
        $this->schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $columns = $this->schema->listTableColumns($this->tableName);

        $fields = [];

        if ($commandType !== 'migration') {
            $skipFields = ['id', 'created_at', 'updated_at', 'deleted_at'];
        } else {
            $skipFields = ['created_at', 'updated_at', 'deleted_at'];
        }

        $foreignKeyConstraints = $this->table->getForeignKeys();

        $foreignKeys = [];
        foreach ($foreignKeyConstraints as $constraint) {
            $foreignKeys[implode(', ', $constraint->getLocalColumns())] = $constraint;
        }

        foreach ($columns as $column) {
            $fieldName = $column->getName();

            if (in_array($fieldName, $skipFields)) {
                continue;
            }

            $rules = [];

            // add required rules
            if ($column->getNotnull()) {
                $rules[] = 'required';
            }

            switch ($column->getType()->getName()) {
                case 'integer':
                    $fieldInput = $this->generateIntFieldInput($fieldName, 'integer', $column);
                    $type = 'number';
                    $rules[] = 'integer';
                    break;
                case 'smallint':
                    $fieldInput = $this->generateIntFieldInput($fieldName, 'smallInteger', $column);
                    $type = 'number';
                    $rules[] = 'integer';
                    break;
                case 'bigint':
                    $fieldInput = $this->generateIntFieldInput($fieldName, 'bigInteger', $column);
                    $type = 'number';
                    $rules[] = 'integer';
                    break;
                case 'boolean':
                    $fieldInput = $this->generateSingleFieldInput($fieldName, 'boolean');
                    $type = 'text';
                    $rules[] = 'boolean';
                    break;
                case 'datetime':
                    $fieldInput = $this->generateSingleFieldInput($fieldName, 'dateTime');
                    $type = 'date';
                    $rules[] = 'date';
                    break;
                case 'datetimetz':
                    $fieldInput = $this->generateSingleFieldInput($fieldName, 'dateTimeTz');
                    $type = 'date';
                    $rules[] = 'date';
                    break;
                case 'date':
                    $fieldInput = $this->generateSingleFieldInput($fieldName, 'date');
                    $type = 'date';
                    $rules[] = 'date';
                    break;
                case 'time':
                    $fieldInput = $this->generateSingleFieldInput($fieldName, 'time');
                    $type = 'text';
                    break;
                case 'decimal':
                    $fieldInput = $this->generateDecimalInput($column, 'decimal');
                    $type = 'number';
                    break;
                case 'float':
                    $fieldInput = $this->generateFloatInput($column);
                    $type = 'number';
                    break;
                case 'string':
                    $fieldInput = $this->generateStringInput($column);
                    $type = 'text';
                    $rules[] = 'string';
                    $rules[] = 'max:'.$column->getLength();
                    break;
                case 'text':
                    $fieldInput = $this->generateTextInput($column);
                    $type = 'textarea';
                    break;
                default:
                    $fieldInput = $this->generateTextInput($column);
                    $type = 'text';
            }

            if (in_array(strtolower($fieldName), ['email', 'password'])) {
                $type = strtolower($fieldName);
                $rules[] = $type;
            }

            if (empty($fieldInput)) {
                continue;
            }

            $fieldInput .= $this->checkForDefault($column);
            $fieldInput .= $this->checkForNullable($column);
            $fieldInput .= $this->checkForUnique($column);

            // add exist rule based on foreign key
            if (isset($foreignKeys[$fieldName])) {
                $constraint = $foreignKeys[$fieldName];
                // check if the number of forieng columns is equal to 1
                if (count($constraint->getForeignColumns()) === 1) {
                    $tableName = $constraint->getForeignTableName();
                    $foreignColumn = current($constraint->getForeignColumns());
                    $rules[] = 'exists:'.$tableName.','.$foreignColumn;
                }
            }

            $fields[] = GeneratorUtils::processFieldInput($fieldInput, $type, implode('|', $rules));
        }

        return $fields;
    }

    /**
     * @param string                       $name
     * @param string                       $type
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function generateIntFieldInput($name, $type, $column)
    {
        $fieldInput = "$name:$type";

        if ($column->getAutoincrement()) {
            $fieldInput .= ',true';
        }

        if ($column->getUnsigned()) {
            $fieldInput .= ',true';
        }

        return $fieldInput;
    }

    private function generateSingleFieldInput($name, $type)
    {
        $fieldInput = "$name:$type";

        return $fieldInput;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function generateDecimalInput($column)
    {
        $fieldInput = $column->getName().':decimal,'.$column->getPrecision().','.$column->getScale();

        return $fieldInput;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function generateFloatInput($column)
    {
        $fieldInput = $column->getName().':float,'.$column->getPrecision().','.$column->getScale();

        return $fieldInput;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     * @param int                          $length
     *
     * @return string
     */
    private function generateStringInput($column)
    {
        $fieldInput = $column->getName().':string,'.$column->getLength();

        return $fieldInput;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function generateTextInput($column)
    {
        $fieldInput = $column->getName().':text';

        return $fieldInput;
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function checkForNullable($column)
    {
        if (!$column->getNotnull()) {
            return ':nullable';
        }

        return '';
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function checkForDefault($column)
    {
        if ($column->getDefault()) {
            return ':default,' . $column->getDefault();
        }

        return '';
    }

    /**
     * @param \Doctrine\DBAL\Schema\Column $column
     *
     * @return string
     */
    private function checkForUnique($column)
    {
        if (in_array($column->getName(), $this->uniqueFields)) {
            return ':unique';
        }

        return '';
    }
}
