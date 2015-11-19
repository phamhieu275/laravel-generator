<?php namespace Bluecode\Generator\Commands;

use Config;
use File;
use Illuminate\Console\Command;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\File\FileHelper;
use Bluecode\Generator\TemplatesHelper;
use Bluecode\Generator\Utils\GeneratorUtils;
use Symfony\Component\Console\Input\InputOption;

class PublisherCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'generate:publish';

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
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['templates', null, InputOption::VALUE_NONE, 'Publish templates'],
            ['all', null, InputOption::VALUE_NONE, 'Publish all options'],
            ['baseRepository', null, InputOption::VALUE_NONE, 'Publish a base repository file.'],
        ];
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
        $templateHelper = new TemplatesHelper();
        $templateData = $templateHelper->getTemplate('Repository', 'base');

        $templateData = GeneratorUtils::fillTemplate(CommandData::getConfigDynamicVariables(), $templateData);

        $fileName = 'Repository.php';
        $filePath = config('generator.path_repository', app_path('Repositories/'));

        $destinationFile = $filePath.$fileName;

        if (file_exists($destinationFile)) {
            $answer = $this->ask('Do you want to overwrite '.$fileName.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        }

        $fileHelper = new FileHelper();
        $fileHelper->writeFile($destinationFile, $templateData);
        $this->comment('Base Repository generated');
        $this->info($fileName);
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
