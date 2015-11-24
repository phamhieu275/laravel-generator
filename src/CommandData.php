<?php namespace Bluecode\Generator;

use Illuminate\Support\Str;
use Bluecode\Generator\Helper\FileHelper;
use Bluecode\Generator\Helper\GeneratorHelper;

class CommandData
{
    public $modelName;
    public $modelNamePlural;
    public $modelNameCamel;
    public $modelNamePluralCamel;
    public $modelNamespace;

    public $tableName;
    public $fromTable;
    public $skipMigration;
    public $fields;
    public $inputFields;
    public $relationships;
    public $tableSetting;

    /** @var  string */
    public $commandType;

    /** @var  GeneratorCommand */
    public $commandObj;

    /** @var FileHelper */
    public $fileHelper;

    /** @var  bool */
    public $useSoftDelete;

    /** @var  bool */
    public $paginate;

    /** @var  string */
    public $rememberToken;

    /** @var  string */
    public $fieldsFile;

    /** @var array */
    public $dynamicVars = [];

    public static $COMMAND_TYPE_SCAFFOLD = 'scaffold';
    public static $COMMAND_TYPE_MODEL = 'model';
    public static $COMMAND_TYPE_MIGRATION = 'migration';

    public function __construct($commandObj)
    {
        $this->commandObj = $commandObj;
        $this->commandType = $commandObj->type;
    }

    // public function initVariables()
    // {
    //     $this->modelNamePlural = Str::plural($this->modelName);
    //     $this->modelNameCamel = Str::camel($this->modelName);
    //     $this->modelNamePluralCamel = Str::camel($this->modelNamePlural);
    //     $this->initDynamicVariables();
    // }

    // public function getInputFields()
    // {
    //     $fields = [];

    //     $this->commandObj->info('Specify fields for the model (skip id & timestamp fields, will be added automatically)');
    //     $this->commandObj->info('Enter exit to finish');

    //     while (true) {
    //         $fieldInputStr = $this->commandObj->ask('Field: (field_name:field_database_type)', '');

    //         if (empty($fieldInputStr) || $fieldInputStr == false || $fieldInputStr == 'exit') {
    //             break;
    //         }

    //         if (!GeneratorUtils::validateFieldInput($fieldInputStr)) {
    //             $this->commandObj->error('Invalid Input. Try again');
    //             continue;
    //         }

    //         $type = $this->commandObj->ask('Enter field html input type (text): ', 'text');

    //         $validations = $this->commandObj->ask('Enter validations: ', false);

    //         $validations = ($validations == false) ? '' : $validations;

    //         $fields[] = GeneratorUtils::processFieldInput($fieldInputStr, $type, $validations);
    //     }

    //     return $fields;
    // }

    // public function initDynamicVariables()
    // {
    //     $this->dynamicVars = self::getConfigDynamicVariables();

    //     $this->dynamicVars = array_merge($this->dynamicVars, [
    //         '$MODEL_NAME$'              => $this->modelName,

    //         '$MODEL_NAME_CAMEL$'        => $this->modelNameCamel,

    //         '$MODEL_NAME_PLURAL$'       => $this->modelNamePlural,

    //         '$MODEL_NAME_PLURAL_CAMEL$' => $this->modelNamePluralCamel,
    //     ]);

    //     if ($this->tableName) {
    //         $this->dynamicVars['$TABLE_NAME$'] = $this->tableName;
    //     } else {
    //         $this->dynamicVars['$TABLE_NAME$'] = snake_case(str_plural($this->modelName));
    //     }

        // // init message
        // if ($this->commandType === static::$COMMAND_TYPE_SCAFFOLD) {
        //     $locale = config('app.locale');
        //     $configMessages = config('generator.message');
        //     if (isset($configMessages[$locale])) {
        //         $messages = $configMessages[$locale];
        //     } else {
        //         $messages = $configMessages['en'];
        //     }
        //     $this->dynamicVars = array_merge([
        //         '$MESSAGE_STORE$'     => "'".str_replace(':model', '$MODEL_NAME$', $messages['store'])."'",

        //         '$MESSAGE_UPDATE$'    => "'".str_replace(':model', '$MODEL_NAME$', $messages['update'])."'",

        //         '$MESSAGE_DELETE$'    => "'".str_replace(':model', '$MODEL_NAME$', $messages['delete'])."'",
                    
        //         '$MESSAGE_NOT_FOUND$' => "'".str_replace(':model', '$MODEL_NAME$', $messages['not_found'])."'",

        //     ], $this->dynamicVars);
        // }
    // }

    // public function addDynamicVariable($name, $val)
    // {
    //     $this->dynamicVars[$name] = $val;
    // }

}
