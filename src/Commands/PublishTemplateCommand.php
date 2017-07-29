<?php namespace Bluecode\Generator\Commands;

use File;
use Illuminate\Console\Command;
use Bluecode\Generator\Generators\RepositoryGenerator;

class PublishTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generator:publish:template';

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
        $templatesPath = __DIR__.'/../../templates';

        $templatesCopyPath = config('generator.path_template');

        $this->copyDirectory($templatesPath, $templatesCopyPath, 'templates');

        $this->info('Template is copied to '. $templatesCopyPath);
    }

    /**
     * Make directory and copy files
     *
     * @param <type> $sourceDir The source dir
     * @param <type> $destinationDir The destination dir
     * @param string $dirName The dir name
     * @return void
     */
    private function copyDirectory($sourceDir, $destinationDir, $dirName)
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
