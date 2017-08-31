<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Bluecode\Generator\Parser\SchemaParser;
use Bluecode\Generator\Traits\AllCommandTrait;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\ActionViewTrait;

class AllMvcGeneratorCommand extends Command
{
    use AllCommandTrait;
    use TemplateTrait;
    use ActionViewTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:all:mvc
        {--overwrite : Force overwriting existing files}
        {--o|only= : The only model list to generate}
        {--e|exclude= : The exclude model list to not generate}
        {--a|actions= : The comma-separated action list}
    ';

     /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate MVC for many model.';

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

        $views = $this->getListView($this->option('actions'));

        foreach ($models as $model) {
            $this->comment("Generate {$model} model class.");
            $this->callSilent('gen:model', [
                'name' => $model,
                '--overwrite' => $this->option('overwrite'),
            ]);

            $controllerName = $this->getControllerName($model);
            $this->comment("Generate {$controllerName} class.");
            $this->callSilent('gen:controller', [
                'name' => $controllerName,
                '--model' => $model,
                '--overwrite' => $this->option('overwrite'),
            ]);

            $this->comment("Generate views for {$controllerName}.");
            foreach ($views as $view) {
                $this->call('gen:view', [
                    'name' => $view,
                    'model' => $model,
                    '--overwrite' => $this->option('overwrite'),
                ]);
            }
        }
    }
}
