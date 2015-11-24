<?php namespace Bluecode\Generator\Commands;

use File;
use Illuminate\Console\Command;
use Bluecode\Generator\Generators\RepositoryGenerator;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:publish {--all} {--templates} {--baseRepository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes a various things of generator package.';

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->publishCommonViews();
            $this->publishTemplates();
            $this->publishBaseRepository();
        } elseif ($this->option('templates')) {
            $this->publishTemplates();
        } elseif ($this->option('baseRepository')) {
            $this->publishBaseRepository();
        } else {
            $this->publishCommonViews();
        }
    }

    /**
     * Publishes templates.
     */
    public function publishTemplates()
    {
        $templatesPath = __DIR__.'/../../templates';

        $templatesCopyPath = base_path('resources/generator-templates');

        $this->publishDirectory($templatesPath, $templatesCopyPath, 'templates');
    }

    /**
     * Publishes common views.
     */
    public function publishCommonViews()
    {
        $viewsPath = __DIR__.'/../../views/common';

        $viewsCopyPath = base_path('resources/views/common');

        $this->publishDirectory($viewsPath, $viewsCopyPath, 'common views');
    }

    public function publishBaseRepository()
    {
        $repositoryGenerator = new RepositoryGenerator($this);
        $repositoryGenerator->generateBaseRepository();
    }

    public function publishFile($sourceFile, $destinationFile, $fileName)
    {
        if (file_exists($destinationFile)) {
            $answer = $this->ask('Do you want to overwrite '.$fileName.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        }

        copy($sourceFile, $destinationFile);

        $this->comment($fileName.' generated');
        $this->info($destinationFile);
    }

    public function publishDirectory($sourceDir, $destinationDir, $dirName)
    {
        if (file_exists($destinationDir)) {
            $answer = $this->ask('Do you want to overwrite '.$dirName.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        } else {
            File::makeDirectory($destinationDir);
        }

        File::copyDirectory($sourceDir, $destinationDir);

        $this->comment($dirName.' published');
        $this->info($destinationDir);
    }
}
