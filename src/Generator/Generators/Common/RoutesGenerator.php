<?php namespace Bluecode\Generator\Generators\Common;

use Config;
use Bluecode\Generator\CommandData;
use Bluecode\Generator\Generators\GeneratorProvider;
use Bluecode\Generator\Utils\GeneratorUtils;

class RoutesGenerator implements GeneratorProvider
{
    /** @var  CommandData */
    private $commandData;

    /** @var string */
    private $path;

    public function __construct($commandData)
    {
        $this->commandData = $commandData;
        $this->path = Config::get('generator.path_routes', app_path('Http/routes.php'));
    }

    public function generate()
    {
        if ($this->commandData->commandType == CommandData::$COMMAND_TYPE_SCAFFOLD) {
            $this->generateScaffoldRoutes();
        }
    }

    private function generateScaffoldRoutes()
    {
        $routeContents = $this->commandData->fileHelper->getFileContents($this->path);

        $templateData = $this->commandData->templatesHelper->getTemplate('scaffold_routes', 'routes');

        $templateData = GeneratorUtils::fillTemplate($this->commandData->dynamicVars, $templateData);

        $routeContents .= "\n\n".$templateData;

        $this->commandData->fileHelper->writeFile($this->path, $routeContents);
        $this->commandData->commandObj->comment("\nroutes.php modified:");
        $this->commandData->commandObj->info('"'.$this->commandData->modelNamePluralCamel.'" route added.');
    }
}
