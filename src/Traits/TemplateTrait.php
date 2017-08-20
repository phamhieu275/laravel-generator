<?php

namespace Bluecode\Generator\Traits;

trait TemplateTrait
{
    /**
     * Gets the template path.
     *
     * @return string The template path.
     */
    public function getTemplatePath()
    {
        $templatePath = config('generator.path.template');
        if (! is_dir($templatePath)) {
            $templatePath = __DIR__ . '/../../templates/';
        }

        return $templatePath;
    }

    /**
     * Get view folder path
     *
     * @param string $modelName The model name
     * @param string $packageName The package name
     * @return string
     */
    public function getViewNamespace($modelName, $packageName = '')
    {
        $viewFolder = str_plural(snake_case($modelName));

        if (! empty($packageName)) {
            return snake_case($packageName) . "::" . $viewFolder;
        }

        return $viewFolder;
    }

    /**
     * Get the controller name.
     *
     * @param string$model The model name
     * @return string
     */
    public function getControllerName($modelName)
    {
        return studly_case(str_plural($modelName)) . 'Controller';
    }

    /**
     * Get the resource name.
     *
     * @param string $modelName The model name
     * @param string $prefix The prefix
     * @return string
     */
    public function getRoutePrefix($modelName, $prefix = '')
    {
        $resourceUrl = str_plural(snake_case($modelName));

        if (! empty($prefix)) {
            return snake_case($prefix) . '.' . $resourceUrl;
        }

        return $resourceUrl;
    }
}
