<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;

class ResourceGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:resource {name : The model class name}';
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

        $this->call('gen:model', $arguments);

        $this->call('gen:controller', [
            'name' => $arguments['name'] . 'Controller',
            '--model' => config('generator.namespace.model') . '\\' . $arguments['name']
        ]);

        $this->call('gen:view', $arguments);
    }
}
