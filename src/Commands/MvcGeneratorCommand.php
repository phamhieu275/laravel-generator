<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\ActionViewTrait;

class MvcGeneratorCommand extends Command
{
    use TemplateTrait;
    use ActionViewTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:mvc
        {model : The name of the model}
        {--overwrite : Force overwriting existing files}
        {--a|actions= : The comma-separated action list}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create mvc for given model';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $modelName = $this->argument('model');

        $this->call('gen:model', [
            'name' => $modelName,
            '--overwrite' => $this->option('overwrite'),
        ]);

        $this->call('gen:controller', [
            'name' => $this->getControllerName($modelName),
            '--model' => config('generator.namespace.model') . '\\' . $modelName,
            '--overwrite' => $this->option('overwrite')
        ]);

        foreach ($this->getListView($this->option('actions')) as $view) {
            $this->call('gen:view', [
                'name' => $view,
                'model' => $modelName,
                '--overwrite' => $this->option('overwrite')
            ]);
        }
    }
}
