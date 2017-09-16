<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;
use Bluecode\Generator\Traits\TemplateTrait;

class ApiGeneratorCommand extends Command
{
    use TemplateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:api
        {model : The name of the model}
        {--overwrite : Force overwriting existing files}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create api controller and resource for given model';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $model = $this->argument('model');
        $modelName = class_basename($model);

        $this->call('gen:model', [
            'name' => $model,
            '--overwrite' => $this->option('overwrite'),
        ]);

        $this->call('gen:controller', [
            'name' => $this->getControllerName($modelName),
            '--api' => true,
            '--model' => $model,
            '--overwrite' => $this->option('overwrite')
        ]);

        $this->call('gen:resource', [
            'name' => $modelName,
            '--overwrite' => $this->option('overwrite')
        ]);

        $this->call('gen:resource', [
            'name' => $modelName . 'Collection',
            '--overwrite' => $this->option('overwrite')
        ]);
    }
}
