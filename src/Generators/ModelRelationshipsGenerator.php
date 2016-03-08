<?php namespace Bluecode\Generator\Generators;

use DB;

class ModelRelationshipsGenerator
{
    /**
     * Default database schema
     *
     * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
     */
    public $schema;

    /**
     * All tables in default database
     *
     * @var \Doctrine\DBAL\Schema\Table
     */
    public $tables;

    /**
     * All relationsips between all tables
     *
     * @var array
     */
    public $relationships;

    public function __construct()
    {
        $this->schema = DB::getDoctrineSchemaManager();
        $this->schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $this->tables = $this->schema->listTables();

        $this->relationships = [];

        //first create empty ruleset for each table
        foreach ($this->tables as $table) {
            $this->relationships[$table->getName()] = [
                'hasMany' => [],
                'hasOne' => [],
                'belongsTo' => [],
                'belongsToMany' => [],
            ];
        }

        // get all relationships into $this->relationships variable
        $this->getAllRelationships();
    }

    public function getFunctionsFromTable($tableName)
    {
        $relationship = $this->relationships[$tableName];

        $functions = array_merge(
            $this->generateHasOneFunctions($relationship['hasOne']),
            $this->generateBelongsToFunctions($relationship['belongsTo']),
            $this->generateHasManyFunctions($relationship['hasMany']),
            $this->generateBelongsToManyFunctions($relationship['belongsToMany'])
        );
        
        return $functions;
    }

    private function getAllRelationships()
    {
        foreach ($this->tables as $table) {
            $tableName = $table->getName();

            // exclude migratations table
            if ($tableName === 'migrations') {
                continue;
            }

            $foreignKeys = $table->getForeignKeys();

            if ($table->hasPrimaryKey()) {
                $primaryColumns = $table->getPrimaryKeyColumns();
                sort($primaryColumns);
            } else {
                $primaryColumns = [];
            }

            $columns = $table->getColumns();

            $isManyToMany = $this->detectManyToMany($table, $primaryColumns, $foreignKeys);

            if ($isManyToMany) {
                $this->addManyToManyRules($table);
            }

            foreach ($foreignKeys as $constraint) {
                $isOneToOne = !empty($primaryColumns) ? $this->detectOneToOne($constraint, $primaryColumns) : false;

                if ($isOneToOne) {
                    $this->addOneToOneRules($tableName, $constraint);
                } else {
                    $this->addOneToManyRules($tableName, $constraint);
                }
            }
        }
    }

    private function detectManyToMany($table, $primaryColumns, $foreignKeys)
    {
        //ensure we only have two foreign keys
        if (count($foreignKeys) === 2) {
            //ensure our foreign keys are not also defined as primary keys
            $primaryKeyCountThatAreAlsoForeignKeys = 0;
            foreach ($foreignKeys as $constraint) {
                $foreignColumns = $constraint->getLocalColumns();

                if (count($foreignColumns) > 1) {
                    continue;
                }

                $foreignColumn = current($foreignColumns);

                foreach ($primaryColumns as $primaryColumn) {
                    if ($primaryColumn === $foreignColumn) {
                        ++$primaryKeyCountThatAreAlsoForeignKeys;
                    }
                }
            }

            if ($primaryKeyCountThatAreAlsoForeignKeys === 1) {
                //one of the keys foreign keys was also a primary key
                //this is not a many to many
                return false;
            }

            //ensure no other tables refer to this one
            foreach ($this->tables as $compareTable) {
                if ($table->getName() === $compareTable->getName()) {
                    continue;
                }

                foreach ($compareTable->getForeignKeys() as $constraint) {
                    if ($constraint->getForeignTableName() === $table->getName()) {
                        return false;
                    }
                }
            }
            //this is a many to many table!
            return true;
        }

        return false;
    }

    private function addManyToManyRules($table)
    {
        $foreign = $table->getForeignKeys();

        // getForeignKeys() method of table return associative Array
        // Convert to simple Array to avoid fatal error where using numeric key
        $foreign = array_values($foreign);

        $fk1 = $foreign[0];
        $fk1Table = $fk1->getForeignTableName();
        $fk1Field = current($fk1->getLocalColumns());

        $fk2 = $foreign[1];
        $fk2Table = $fk2->getForeignTableName();
        $fk2Field = current($fk2->getLocalColumns());

        //User belongstomany groups user_group, user_id, group_id
        // Use getName() method on table to get name of table.
        $this->relationships[$fk1Table]['belongsToMany'][] = [$fk2Table, $table->getName(), $fk1Field, $fk2Field];
        $this->relationships[$fk2Table]['belongsToMany'][] = [$fk1Table, $table->getName(), $fk2Field, $fk1Field];
    }

    /**
     * if FK is also a primary key, and there is only one primary key
     * we know this will be a one to one relationship
     *
     * @param  object $constraint
     * @param  array $primaryFields
     * @return boolean
     */
    private function detectOneToOne($constraint, $primaryColumns)
    {
        $foreignColumns = $constraint->getLocalColumns();
        sort($foreignColumns);

        return $primaryColumns === $foreignColumns;
    }

    private function addOneToOneRules($table, $constraint)
    {
        $fkTable = $constraint->getForeignTableName();

        $fields = $constraint->getLocalColumns();
        $references = $constraint->getForeignColumns();

        if (count($fields) > 1 || count($references) > 1) {
            return;
        }

        $this->relationships[$table]['belongsTo'][] = [$fkTable, current($fields), current($references)];
        $this->relationships[$fkTable]['hasOne'][] = [$table, current($fields), current($references)];
    }

    private function addOneToManyRules($table, $constraint)
    {
        $fkTable = $constraint->getForeignTableName();

        $fields = $constraint->getLocalColumns();
        $references = $constraint->getForeignColumns();

        if (count($fields) > 1 || count($references) > 1) {
            return;
        }

        $this->relationships[$table]['belongsTo'][] = [$fkTable, current($fields), current($references)];
        $this->relationships[$fkTable]['hasMany'][] = [$table, current($fields), current($references)];
    }

    private function generateHasOneFunctions($rulesContainer)
    {
        $functions = [];

        foreach ($rulesContainer as $rules) {
            $hasOneModel = $this->generateModelNameFromTableName($rules[0]);
            $key1 = $rules[1];
            $key2 = $rules[2];

            $hasOneFunctionName = $this->getSingularFunctionName($rules[0]);

            $function  = "public function $hasOneFunctionName() {";
            $function .= "\n\t\treturn \$this->hasOne('$hasOneModel', '$key1', '$key2');";
            $function .= "\n\t}";

            $functions[] = $function;
        }

        return $functions;
    }

    private function generateBelongsToFunctions($rulesContainer)
    {
        $functions = [];

        foreach ($rulesContainer as $rules) {
            $belongsToModel = $this->generateModelNameFromTableName($rules[0]);
            $key1 = $rules[1];
            $key2 = $rules[2];

            $belongsToFunctionName = $this->getSingularFunctionName($rules[0]);

            $function  = "public function $belongsToFunctionName() {";
            $function .= "\n\t\treturn \$this->belongsTo('$belongsToModel', '$key1', '$key2');";
            $function .= "\n\t}";

            $functions[] = $function;
        }

        return $functions;
    }

    private function generateHasManyFunctions($rulesContainer)
    {
        $functions = [];

        foreach ($rulesContainer as $rules) {
            $hasManyModel = $this->generateModelNameFromTableName($rules[0]);
            $key1 = $rules[1];
            $key2 = $rules[2];

            $hasManyFunctionName = $this->getPluralFunctionName($rules[0]);

            $function  = "public function $hasManyFunctionName() {";
            $function .= "\n\t\treturn \$this->hasMany('$hasManyModel', '$key1', '$key2');";
            $function .= "\n\t}";

            $functions[] = $function;
        }

        return $functions;
    }

    private function generateBelongsToManyFunctions($rulesContainer)
    {
        $functions = [];

        foreach ($rulesContainer as $rules) {
            $belongsToManyModel = $this->generateModelNameFromTableName($rules[0]);
            $through = $rules[1];
            $key1 = $rules[2];
            $key2 = $rules[3];

            $belongsToManyFunctionName = $this->getPluralFunctionName($rules[0]);

            $function  = "public function $belongsToManyFunctionName() {";
            $function .= "\n\t\treturn \$this->belongsToMany('$belongsToManyModel', '$through', '$key1', '$key2');";
            $function .= "\n\t}";

            $functions[] = $function;
        }

        return $functions;
    }

    private function getPluralFunctionName($modelName)
    {
        return str_plural(camel_case($modelName));
    }

    private function getSingularFunctionName($modelName)
    {
        return str_singular(camel_case($modelName));
    }

    private function generateModelNameFromTableName($table)
    {
        $modelName = ucfirst(camel_case(str_singular($table)));
        $modelNameSpace = config('generate.namespace_model', 'App\Models');

        return "$modelNameSpace\\$modelName";
    }
}
