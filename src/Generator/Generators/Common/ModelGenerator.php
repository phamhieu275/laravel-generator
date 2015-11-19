<?php namespace Bluecode\Generator\Generators\Common;

use Config;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\GeneratorProvider;
use Bluecode\Generator\Utils\GeneratorUtils;

class ModelGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_model', app_path('Models/'));
    }

    public function generate()
    {
        $templateName = 'Model';

        $templateData = $this->commandData->templatesHelper->getTemplate($templateName, 'common');

        $templateData = $this->fillTemplate($templateData);

        $fileName = $this->commandData->modelName.'.php';

        if (!file_exists($this->path)) {
            mkdir($this->path, 0755, true);
        }

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nModel created: ");
        $this->commandData->commandObj->info($fileName);
    }

    private function fillTemplate($templateData)
    {
        $variables = $this->commandData->dynamicVars;

        $importTraits = $traits = [];
        if ($this->commandData->useSoftDelete) {
            array_push($importTraits, $variables['$SOFT_DELETE_IMPORT$']);
            array_push($traits, $variables['$SOFT_DELETE_TRAIT$']);
        }

        if ($this->commandData->useAuth) {
            $importTraits = array_merge($importTraits, $variables['$AUTH_IMPORT$']);
            $traits = array_merge($traits, $variables['$AUTH_TRAIT$']);
        } else {
            $templateData = str_replace('$AUTH_IMPLEMENTS$', '', $templateData);
        }

        if (!empty($importTraits)) {
            $variables['$IMPORT_TRAIT$'] = implode(PHP_EOL, $importTraits)."\n";
        } else {
            $variables['$IMPORT_TRAIT$'] = '';
        }

        if (!empty($traits)) {
            $variables['$USE_TRAIT$'] = "use ".implode(", ", $traits).";\n";
        } else {
            $variables['$USE_TRAIT$'] = '';
        }

        $templateData = GeneratorUtils::fillTemplate($variables, $templateData);

        $fillables = [];

        foreach ($this->commandData->inputFields as $field) {
            $fillables[] = '"'.$field['fieldName'].'"';
        }

        $templateData = str_replace('$FIELDS$', implode(",\n\t\t", $fillables), $templateData);

        $templateData = str_replace('$RULES$', implode(",\n\t\t", $this->generateRules()), $templateData);

        $templateData = str_replace('$CAST$', implode(",\n\t\t", $this->generateCasts()), $templateData);

        $templateData = str_replace('$RELATIONSHIPS$', $this->generateRelationships(), $templateData);

        return $templateData;
    }

    private function generateRules()
    {
        $rules = [];

        foreach ($this->commandData->inputFields as $field) {
            if (empty($field['validations'])) {
                continue;
            }

            $rule = '"'.$field['fieldName'].'" => "'.$field['validations'].'"';
            $rules[] = $rule;
        }

        return $rules;
    }

    public function generateCasts()
    {
        $casts = [];

        foreach ($this->commandData->inputFields as $field) {
            switch ($field['fieldType']) {
                case 'integer':
                    $rule = '"'.$field['fieldName'].'" => "integer"';
                    break;
                case 'double':
                    $rule = '"'.$field['fieldName'].'" => "double"';
                    break;
                case 'float':
                    $rule = '"'.$field['fieldName'].'" => "float"';
                    break;
                case 'boolean':
                    $rule = '"'.$field['fieldName'].'" => "boolean"';
                    break;
                case 'string':
                case 'char':
                case 'text':
                    $rule = '"'.$field['fieldName'].'" => "string"';
                    break;
                default:
                    $rule = '';
                    break;
            }

            if (!empty($rule)) {
                $casts[] = $rule;
            }
        }

        return $casts;
    }

    public function generateRelationships()
    {
        return implode("\n", $this->commandData->relationships);
    }
}
