<?php namespace Bluecode\Generator\Generators;

use Bluecode\Generator\Syntax\AddToTable;
use Bluecode\Generator\Syntax\AddForeignKeysToTable;
use Bluecode\Generator\Syntax\RemoveForeignKeysFromTable;
use Bluecode\Generator\Parser\SchemaParser;

class MigrationGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Filename date prefix (Y_m_d_His)
     * @var string
     */
    public $datePrefix;

    public function __construct($command)
    {
        parent::__construct($command);
        $this->schemaParser = new SchemaParser();
    }

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'migration';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'common/Migration';
    }

    public function generate($data = [])
    {
        $this->datePrefix = date('Y_m_d_His');
        $this->generateMigrationFile('create', $this->command->tables);

        $this->command->info("\nSetting up Foreign Key Migrations\n");
        $this->templatePath = 'common/Migration_ForeignKey';
        $this->datePrefix = date('Y_m_d_His', strtotime('+1 second'));
        $this->generateMigrationFile('foreign_keys', $this->command->tables);
    }

    /**
     * Generate Migrations
     *
     * @param  string $method Create Tables or Foreign Keys ['create', 'foreign_keys']
     * @param  array  $tables List of tables to create migrations for
     * @throws MethodNotFoundException
     * @return void
     */
    public function generateMigrationFile($method, $tables)
    {
        if ($method == 'create') {
            $function = 'getFields';
            $prefix = 'create';
        } elseif ($method = 'foreign_keys') {
            $function = 'getForeignKeyConstraints';
            $prefix = 'add_foreign_keys_to';
            $method = 'table';
        } else {
            return;
        }

        $this->method = $method;
        foreach ($tables as $table) {
            $migrationName = $prefix . '_' . $table . '_table';
            
            $fields = $this->schemaParser->{$function}($table);

            if (empty($fields)) {
                continue;
            }

            $filename = $this->datePrefix . '_' . $migrationName.'.php';

            $templateData = $this->getTemplateData($fields, [
                'CLASS'  => ucwords(camel_case($migrationName)),
                'TABLE'  => $table,
                'METHOD' => $method,
            ]);

            $this->generateFile($filename, $templateData);
        }
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    public function getTemplateData($fields, $data = [])
    {
        if ($data['METHOD'] == 'create') {
            $up = (new AddToTable)->run($fields, $data['TABLE']);
            $down = '';
        } else {
            $up = (new AddForeignKeysToTable)->run($fields, $data['TABLE']);
            $down = (new RemoveForeignKeysFromTable)->run($fields, $data['TABLE']);
        }

        return array_merge($data, [
            'UP'         => $up,
            'DOWN'       => $down
        ]);
    }
}
