<?php namespace Bluecode\Generator\Generators\Scaffold;

use Config;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\GeneratorProvider;
use Bluecode\Generator\Utils\GeneratorUtils;

class ViewControllerGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_controller', app_path('Http/Controllers/'));
    }

    public function generate()
    {
        // get template filename
        $useRepositoryLayer = Config::get('generator.use_repository_layer', true);
        $useServiceLayer = Config::get('generator.use_service_layer', true);
        if ($useServiceLayer && $useRepositoryLayer) {
            $templateFilename = 'Controller_Service';
        } elseif ($useRepositoryLayer) {
            $templateFilename = 'Controller_Repository';
        } else {
            $templateFilename = 'Controller';
        }

        $templateData = $this->commandData->templatesHelper->getTemplate($templateFilename, 'scaffold');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        if ($this->commandData->paginate) {
            $templateData = str_replace('$RENDER_TYPE$', 'paginate('.$this->commandData->paginate.')', $templateData);
        } else {
            $templateData = str_replace('$RENDER_TYPE$', 'all()', $templateData);
        }

        $fileName = $this->commandData->modelName.'Controller.php';

        $path = $this->path.$fileName;

        $this->commandData->fileHelper->writeFile($path, $templateData);
        $this->commandData->commandObj->comment("\nController created: ");
        $this->commandData->commandObj->info($fileName);
    }
}
