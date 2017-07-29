<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;

class ResourceMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:resource {name : The model class name}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create crud for given table.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $arguments = $this->arguments();

        $this->call('generator:make:model', $arguments);

        $this->call('generator:make:controller', [
            'name' => $arguments['name'] . 'Controller',
            '--model' => config('generator.namespace_model') . '\\' . $arguments['name']
        ]);

        $this->call('generator:make:view', $arguments);
    }
}
