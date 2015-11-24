<?php namespace Bluecode\Generator\Generators;

class RoutesGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'route';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'scaffold/Route';
    }

    public function generate($data = [])
    {
        $data['RESOURCE_URL'] = str_slug($data['TABLE_NAME']);
        $routeContent = "\n\n".$this->generateContent($this->templatePath, $data);

        $this->command->info("\nUpdate route for resources:".$data['TABLE_NAME']);

        $this->fileHelper->append($this->rootPath, $routeContent);
    }
}
