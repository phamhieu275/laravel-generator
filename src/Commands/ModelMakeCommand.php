<?php namespace Bluecode\Generator\Commands;

use Bluecode\Generator\Generators\ModelGenerator;

class ModelMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:model
                                {tables?} : List table name for generate model files.}
                                {--tables= : List table name for generate model files.}
                                {--ignore= : List ignore table name.}
                                {--models= : List model name for generate.}
                                {--auth : Use Authenticatable, Authorizable, CanResetPassword trait}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model file with validation and relationships for given table.';
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
        return 'model';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        if ($this->option('models')) {
            $this->models = explode(',', $this->option('models'));
        }

        // TODO: compare the length option

        $this->comment('Generating models for: '. implode(',', $this->tables));

        $configData = $this->getConfigData();

        $modelGenerator = new ModelGenerator($this);

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
            
            $modelGenerator->generate($data);
        }
    }
}
