<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Traits\AllCommandTrait;

class AllModelGeneratorCommand extends Command
{
    use AllCommandTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:all:model
        {--overwrite : Force overwriting existing files}
        {--o|only= : The only model list to generate}
        {--e|exclude= : The exclude model list to not generate}
        {--p|path= : The location where models is generated}
    ';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate many model class.';

    /**
     * Create new console command
     *
     * @param \Bluecode\Generator\Parser\SchemaParser $schemaParser The schema parser
     * @return void
     */
    public function __construct(SchemaParser $schemaParser)
    {
        parent::__construct();
        $this->schema = $schemaParser;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $models = $this->getListModel();

        $path = $this->option('path') ? rtrim(trim($this->option('path')), '/') . '/' : '';
        foreach ($models as $model) {
            $this->comment("Generate {$model} model class.");
            $this->callSilent('gen:model', [
                'name' => $path . $model,
                '--overwrite' => $this->option('overwrite'),
            ]);
        }
    }
}
