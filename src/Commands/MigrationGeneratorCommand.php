<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Support\Composer;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Bluecode\Generator\Creator\MigrationCreator;

class MigrationGeneratorCommand extends MigrateMakeCommand
{
    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:migration
        {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
    ';

    /**
     * Create a new migration install command instance.
     *
     * @param \Illuminate\Database\Migrations\MigrationCreator $creator
     * @param \Illuminate\Support\Composer $composer The composer
     * @return void
     */
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return $this->laravel->basePath().'/'.$targetPath;
        }

        return config('generator.path.migration');
    }
}
