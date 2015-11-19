<?php namespace Bluecode\Generator\Generators\Common;

use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\GeneratorProvider;
use Bluecode\Generator\Utils\GeneratorUtils;

class MigrationGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = config('generator.path_migration', base_path('database/migrations/'));
    }

    public function generate()
    {
        $templateData = $this->commandData->templatesHelper->getTemplate('Migration', 'common');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $templateData = str_replace('$FIELDS$', $this->generateFieldsStr(), $templateData);

        $fileName = date('Y_m_d_His').'_'.'create_'.$this->commandData->tableName.'_table.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nMigration created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function generateFieldsStr()
    {
        $fields = [];
        if ($this->commandData->commandType !== 'migration') {
            $fields[] = '$table->increments("id");';
        }

        foreach ($this->commandData->inputFields as $field) {
            $fields[] = GeneratorUtils::createField($field['fieldInput']);
        }

        if ($this->commandData->rememberToken) {
            $fields[] = '$table->rememberToken();';
        }

        $fields[] = '$table->timestamps();';

        if ($this->commandData->useSoftDelete) {
            $fields[] = '$table->softDeletes();';
        }

        $fieldsStr = implode("\n\t\t\t", $fields);

        return $fieldsStr;
    }
}
