<?php namespace Bluecode\Generator;

use Config;
use Illuminate\Support\Str;
use Bluecode\Generator\File\FileHelper;
use Bluecode\Generator\Utils\GeneratorUtils;

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
    public $inputFields;
    public $relationships;

    /** @var  string */
    public $commandType;

    /** @var  GeneratorCommand */
    public $commandObj;

    /** @var FileHelper */
    public $fileHelper;

    /** @var TemplatesHelper */
    public $templatesHelper;

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

    public function __construct($commandObj, $commandType)
    {
        $this->commandObj = $commandObj;
        $this->commandType = $commandType;
        $this->fileHelper = new FileHelper();
        $this->templatesHelper = new TemplatesHelper();
    }

    public function initVariables()
    {
        $this->modelNamePlural = Str::plural($this->modelName);
        $this->modelNameCamel = Str::camel($this->modelName);
        $this->modelNamePluralCamel = Str::camel($this->modelNamePlural);
        $this->initDynamicVariables();
    }

    public function getInputFields()
    {
        $fields = [];

        $this->commandObj->info('Specify fields for the model (skip id & timestamp fields, will be added automatically)');
        $this->commandObj->info('Enter exit to finish');

        while (true) {
            $fieldInputStr = $this->commandObj->ask('Field: (field_name:field_database_type)', '');

            if (empty($fieldInputStr) || $fieldInputStr == false || $fieldInputStr == 'exit') {
                break;
            }

            if (!GeneratorUtils::validateFieldInput($fieldInputStr)) {
                $this->commandObj->error('Invalid Input. Try again');
                continue;
            }

            $type = $this->commandObj->ask('Enter field html input type (text): ', 'text');

            $validations = $this->commandObj->ask('Enter validations: ', false);

            $validations = ($validations == false) ? '' : $validations;

            $fields[] = GeneratorUtils::processFieldInput($fieldInputStr, $type, $validations);
        }

        return $fields;
    }

    public function initDynamicVariables()
    {
        $this->dynamicVars = self::getConfigDynamicVariables();

        $this->dynamicVars = array_merge($this->dynamicVars, [
            '$MODEL_NAME$'              => $this->modelName,

            '$MODEL_NAME_CAMEL$'        => $this->modelNameCamel,

            '$MODEL_NAME_PLURAL$'       => $this->modelNamePlural,

            '$MODEL_NAME_PLURAL_CAMEL$' => $this->modelNamePluralCamel,
        ]);

        if ($this->tableName) {
            $this->dynamicVars['$TABLE_NAME$'] = $this->tableName;
        } else {
            $this->dynamicVars['$TABLE_NAME$'] = snake_case(str_plural($this->modelName));
        }

        // init message
        if ($this->commandType === static::$COMMAND_TYPE_SCAFFOLD) {
            $locale = config('app.locale');
            $configMessages = config('generator.message');
            if (isset($configMessages[$locale])) {
                $messages = $configMessages[$locale];
            } else {
                $messages = $configMessages['en'];
            }
            $this->dynamicVars = array_merge([
                '$MESSAGE_STORE$'     => "'".str_replace(':model', '$MODEL_NAME$', $messages['store'])."'",

                '$MESSAGE_UPDATE$'    => "'".str_replace(':model', '$MODEL_NAME$', $messages['update'])."'",

                '$MESSAGE_DELETE$'    => "'".str_replace(':model', '$MODEL_NAME$', $messages['delete'])."'",
                    
                '$MESSAGE_NOT_FOUND$' => "'".str_replace(':model', '$MODEL_NAME$', $messages['not_found'])."'",

            ], $this->dynamicVars);
        }
    }

    public function addDynamicVariable($name, $val)
    {
        $this->dynamicVars[$name] = $val;
    }

    public static function getConfigDynamicVariables()
    {
        $viewConfigPath = Config::get('generator.path_views', base_path('resources/views/'));
        $viewBasePath = base_path('resources/views/');

        if ($viewBasePath === $viewConfigPath) {
            $viewPath = '';
        } else {
            $trans = array('/' => '.', $viewBasePath => '');
            $viewPath = strtr($viewConfigPath, $trans);
        }

        $routePrefix = Config::get('generator.route_prefix', '');
        // check route prefix end with '.'
        if (strlen($routePrefix) > 0 && $routePrefix[strlen($routePrefix) - 1] !== '.') {
            $routePrefix .= '.';
        }

        $authImport = [
            'use Illuminate\Auth\Authenticatable;',
            'use Illuminate\Auth\Passwords\CanResetPassword;',
            'use Illuminate\Foundation\Auth\Access\Authorizable;',
            'use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;',
            'use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;',
            'use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;',
        ];

        $authTrait = ['Authenticatable', 'Authorizable', 'CanResetPassword'];

        return [

            '$BASE_CONTROLLER$'         => Config::get('generator.base_controller', 'App\Http\Controllers\Controller'),

            '$NAMESPACE_CONTROLLER$'    => Config::get('generator.namespace_controller', 'App\Http\Controllers'),

            '$NAMESPACE_REQUEST$'       => Config::get('generator.namespace_request', 'App\Http\Requests'),

            '$NAMESPACE_REPOSITORY$'    => Config::get('generator.namespace_repository', 'App\Repositories'),

            '$NAMESPACE_SERVICE$'       => Config::get('generator.namespace_service', 'App\Services'),

            '$NAMESPACE_MODEL$'         => Config::get('generator.namespace_model', 'App\Models'),

            '$NAMESPACE_MODEL_EXTEND$'  => Config::get('generator.model_extend_class', 'Illuminate\Database\Eloquent\Model'),

            '$IMPORT_TRAIT$'            => '',

            '$USE_TRAIT$'               => '',

            '$AUTH_IMPORT$'             => $authImport,

            '$AUTH_TRAIT$'              => $authTrait,

            '$AUTH_IMPLEMENTS$'         => ' implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract',

            '$SOFT_DELETE_TRAIT$'       => 'SoftDeletes',

            '$SOFT_DELETE_IMPORT$'      => "use Illuminate\Database\Eloquent\SoftDeletes;",

            '$MAIN_LAYOUT$'             => Config::get('generator.main_layout', 'app'),

            '$VIEW_PATH$'               => $viewPath,

            '$ROUTE_PREFIX$'            => $routePrefix,

            '$PRIMARY_KEY$'             => 'id',
        ];
    }
}
