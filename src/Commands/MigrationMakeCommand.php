<?php namespace Bluecode\Generator\Commands;

use Bluecode\Generator\Generators\MigrationGenerator;

class MigrationMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:make:migration
                                {tables?} : List table name for generate migration files.}
                                {--tables= : List table name for generate migration files.}
                                {--ignore= : List ignore table name.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migrate from exist tables';

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'migration';
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();

        $this->comment('Generating migrations for: '. implode(', ', $this->tables));

        $migrationGenerator = new MigrationGenerator($this);
        $migrationGenerator->generate();
    }
}
