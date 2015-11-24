<?php namespace Bluecode\Generator\Generators;

class RepositoryGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'repository';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'scaffold/Repository';
    }

    public function generate($data = [])
    {
        $filename = $data['MODEL_NAME'].'Repository.php';

        $this->generateFile($filename, $data);
    }

    public function generateBaseRepository()
    {
        $filename = 'Repository.php';
        $destPath = $this->rootPath.$filename;

        if (file_exists($destPath)) {
            $answer = $this->command->ask('Do you want to overwrite '.$filename.'? (y|N) :', false);

            if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
                return;
            }
        }

        $templateData = [
            'NAMESPACE_REPOSITORY' => config('generator.namespace_repository')
        ];

        $this->generateFile($filename, $templateData, 'base/repository');
    }

    public function askMakeBaseRepository()
    {
        $filename = 'Repository.php';
        $destPath = $this->rootPath.$filename;

        if (file_exists($destPath)) {
            return;
        }

        $answer = $this->command->ask('Do you want to create the base repository file ? (y|N) :', false);

        if (strtolower($answer) != 'y' and strtolower($answer) != 'yes') {
            return;
        }

        $this->command->callSilent('generator:publish', ['--baseRepository' => true]);
    }
}
