<?php namespace Bluecode\Generator\Commands;

use Config;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\Common\MigrationGenerator;
use Bluecode\Generator\Generators\Common\RoutesGenerator;
use Bluecode\Generator\Generators\Common\RequestGenerator;
use Bluecode\Generator\Generators\Common\ModelGenerator;
use Bluecode\Generator\Generators\Common\RepositoryGenerator;
use Bluecode\Generator\Generators\Common\ServiceGenerator;
use Bluecode\Generator\Generators\Scaffold\ViewControllerGenerator;
use Bluecode\Generator\Generators\Scaffold\ViewGenerator;

class ScaffoldGeneratorCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:scaffold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a full CRUD for given model with initial views.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->commandData = new CommandData($this, CommandData::$COMMAND_TYPE_SCAFFOLD);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $routeGenerator = new RoutesGenerator($this->commandData);
        $routeGenerator->generate();

        $requestGenerator = new RequestGenerator($this->commandData);
        $requestGenerator->generate();

        $modelGenerator = new ModelGenerator($this->commandData);
        $modelGenerator->generate();

        $useRepositoryLayer = Config::get('generator.use_repository_layer', true);
        if ($useRepositoryLayer) {
            $repositoryGenerator = new RepositoryGenerator($this->commandData);
            $repositoryGenerator->generate();
        }

        $useServiceLayer = Config::get('generator.use_service_layer', true);
        if ($useServiceLayer) {
            $serviceGenerator = new ServiceGenerator($this->commandData);
            $serviceGenerator->generate();
        }

        $repoControllerGenerator = new ViewControllerGenerator($this->commandData);
        $repoControllerGenerator->generate();

        $viewsGenerator = new ViewGenerator($this->commandData);
        $viewsGenerator->generate();

        if (!$this->commandData->skipMigration and !$this->commandData->fromTable) {
            $migrationGenerator = new MigrationGenerator($this->commandData);
            $migrationGenerator->generate();
            if ($this->confirm("\nDo you want to migrate database? [y|N]", false)) {
                $this->call('migrate');
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array_merge(parent::getArguments());
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return array_merge(parent::getOptions(), []);
    }
}
