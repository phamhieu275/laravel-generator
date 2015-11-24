<?php namespace Bluecode\Generator\Commands;

use Illuminate\Console\Command;

class ResourceMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:resource
                                {tables?} : List table name for generate resource files.}
                                {--tables= : List table name for generate resource files.}
                                {--ignore= : List ignore table name.}
                                {--models= : List model name for seed.}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create migration, CRUD, factory for given table.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $params = [
            'tables'   => $this->argument('tables'),
            '--tables' => $this->option('tables'),
            '--ignore' => $this->option('ignore'),
        ];

        $this->call('generator:make:migration', $params);

        $params['--models'] = $this->option('models');

        $this->call('generator:make:scaffold', $params);

        $this->call('generator:make:factory', $params);
    }
}
