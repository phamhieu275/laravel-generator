<?php namespace Bluecode\Generator\Commands;

use DB;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\Common\MigrationGenerator;
use Bluecode\Generator\Utils\TableFieldsGenerator;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migrate from exist tables';

    /**
     * Array of Fields to create in a new Migration
     * Namely: Columns, Indexes and Foreign Keys
     * @var array
     */
    protected $fields = array();

    /**
     * List of Migrations that has been done
     * @var array
     */
    protected $migrations = array();

    /**
     * @var bool
     */
    protected $log = false;

    /**
     * @var int
     */
    protected $batch;

    /**
     * Filename date prefix (Y_m_d_His)
     * @var string
     */
    protected $datePrefix;

    /**
     * @var string
     */
    protected $migrationName;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $table;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_MIGRATION);
        // $this->repository = $repository;
    }

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
            $tables = $this->getTables();
        }
        $this->commandData->useSoftDelete = $this->option('softDelete');
        $this->commandData->rememberToken = $this->option('rememberToken');

        $tables = $this->removeExcludedTables($tables);
        $this->info('Generating migrations for: '. implode(', ', $tables));

        // if (!$this->option('no-interaction')) {
        //     $this->log = $this->askYn('Do you want to log these migrations in the migrations table?');
        // }

        // if ($this->log) {
        //     $this->repository->setSource($this->option('connection'));
        //     if (! $this->repository->repositoryExists()) {
        //         $options = array('--database' => $this->option('connection') );
        //         $this->call('migrate:install', $options);
        //     }
        //     $batch = $this->repository->getNextBatchNumber();
        //     $this->batch = $this->askNumeric('Next Batch Number is: '. $batch .'. We recommend using Batch Number 0 so that it becomes the "first" migration', 0);
        // }

        foreach ($tables as $table) {
            $tableFieldsGenerator = new TableFieldsGenerator($table);
            $this->commandData->inputFields = $tableFieldsGenerator->generateFieldsFromTable($this->commandData->commandType);

            $this->commandData->tableName = $table;
            $this->commandData->addDynamicVariable('$TABLE_NAME$', $table);
            $this->commandData->addDynamicVariable('$MODEL_NAME_PLURAL$', studly_case($table));
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
        }        
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['tables', InputArgument::OPTIONAL, 'A list of Tables you wish to Generate Migrations'],
        ];
    }
    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['connection', 'c', InputOption::VALUE_OPTIONAL, 'used database connection', config('database.default')],
            ['tables', 't', InputOption::VALUE_OPTIONAL, 'A list of Tables you wish to generate migrations'],
            ['ignore', 'i', InputOption::VALUE_OPTIONAL, 'A list of Tables you wish to ignore'],
            ['softDelete', null, InputOption::VALUE_NONE, 'Use Soft Delete trait'],
            ['rememberToken', null, InputOption::VALUE_NONE, 'Generate rememberToken field in migration'],
        ];
    }

    protected function getTables() {
        $connection = DB::connection(config('database.default'))->getDoctrineConnection();
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping('bit', 'boolean');

        return $connection->getSchemaManager()->listTableNames();
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
}
