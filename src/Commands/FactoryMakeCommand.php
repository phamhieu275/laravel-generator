<?php namespace Bluecode\Generator\Commands;

use Bluecode\Generator\Generators\FactoryGenerator;

class FactoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:factory
                                {tables?} : List table name for generate migration files.}
                                {--tables= : List table name for generate migration files.}
                                {--ignore= : List ignore table name.}
                                {--models= : List model name for seed.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create factory setup for given table.';
    /**
     * A list model name for generate
     *
     * @var array
     */
    public $models = [];

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'factory';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->comment('\nGenerating factory for : '. implode(',', $this->tables));

        if ($this->option('models')) {
            $this->models = explode(',', $this->option('models'));
        }

        $configData = $this->getConfigData();

        $factoryGenerator = new FactoryGenerator($this);

        foreach ($this->tables as $idx => $tableName) {
            if (isset($this->models[$idx])) {
                $modelName = $this->models[$idx];
            } else {
                $modelName = str_singular(studly_case($tableName));
            }

            $data = array_merge([
                'TABLE_NAME' => $tableName,
                'MODEL_NAME' => $modelName
            ], $configData);
            
            $factoryGenerator->generate($data);
        }
    }
}
