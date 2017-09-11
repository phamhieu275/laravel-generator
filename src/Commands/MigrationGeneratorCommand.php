<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Support\Composer;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
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
    public function __construct(BaseMigrationCreator $baseCreator, Composer $composer, MigrationCreator $creator)
    {
        parent::__construct($baseCreator, $composer);
        $this->creator = $creator;
    }
}
