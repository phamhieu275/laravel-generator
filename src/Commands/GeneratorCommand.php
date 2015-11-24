<?php namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Bluecode\Generator\CommandData;
use DB;

abstract class GeneratorCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * A list of ignore tables
     *
     * @var array
     */
    public $ignoreTables = ['migrations'];
    /**
     * A list of target tables
     *
     * @var array
     */
    public $tables = [];
    /**
     * The data of command
     *
     * @var [type]
     */
    public $commandData;
    /**
     * The type of class being generated.
     *
     * @var string
     */
    public $type;

    /**
     * Get the type of command
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->argument('tables')) {
            $tables = explode(',', $this->argument('tables'));
        } elseif ($this->option('tables')) {
            $tables = explode(',', $this->option('tables'));
        } else {
            $tables = DB::getDoctrineConnection()->getSchemaManager()->listTableNames();
        }

        $this->tables = $this->removeExcludedTables($tables);

        $this->type = $this->getType();
    }

    /**
     * Remove all the tables to exclude from the array of tables
     *
     * @param $tables
     *
     * @return array
     */
    protected function removeExcludedTables($tables)
    {
        $excludes = $this->getExcludedTables();
        $tables = array_diff($tables, $excludes);

        return $tables;
    }
    /**
     * Get a list of tables to exclude
     *
     * @return array
     */
    protected function getExcludedTables()
    {
        $excludes = ['migrations'];
        $ignore = $this->option('ignore');
        if (!empty($ignore)) {
            return array_merge($excludes, explode(',', $ignore));
        }

        return $excludes;
    }
    
    /**
     * get config data from config/generator.php
     *
     * @return array
     */
    public function getConfigData()
    {
        $viewConfigPath = config('generator.path_view', base_path('resources/views/'));
        $viewBasePath = base_path('resources/views/');

        if ($viewBasePath === $viewConfigPath) {
            $viewPath = '';
        } else {
            $trans = array('/' => '.', $viewBasePath => '');
            $viewPath = strtr($viewConfigPath, $trans);
        }

        $routePrefix = config('generator.route_prefix', '');
        // check route prefix end with '.'
        if (strlen($routePrefix) > 0 && $routePrefix[strlen($routePrefix) - 1] !== '.') {
            $routePrefix .= '.';
        }

        return [
            'BASE_CONTROLLER'        => config('generator.base_controller', 'App\Http\Controllers\Controller'),

            'NAMESPACE_MODEL'        => config('generator.namespace_model', 'App\Models'),

            'NAMESPACE_MODEL_EXTEND' => config('generator.model_extend_class', 'Illuminate\Database\Eloquent\Model'),

            'NAMESPACE_CONTROLLER'   => config('generator.namespace_controller', 'App\Http\Controllers'),

            'NAMESPACE_REQUEST'      => config('generator.namespace_request', 'App\Http\Requests'),

            'NAMESPACE_REPOSITORY'   => config('generator.namespace_repository', 'App\Repositories'),

            'NAMESPACE_SERVICE'      => config('generator.namespace_service', 'App\Services'),

            'MAIN_LAYOUT'            => config('generator.main_layout', 'app'),

            'VIEW_PATH'              => $viewPath,

            'ROUTE_PREFIX'           => $routePrefix,
        ];
    }
}
