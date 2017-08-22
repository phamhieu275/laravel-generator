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
        $templatePath = config('generator.path.templates');

        if (! is_dir($templatePath)) {
            return __DIR__ . '/../../templates';
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
        $viewFolder = str_plural(snake_case(strtolower($modelName)));

        if (! empty($packageName)) {
            return snake_case($packageName) . "::" . $viewFolder;
        }

        return $viewFolder;
    }

    /**
     * Ceate the controller name from the model name
     * Format the controller name by studly case and singular
     *
     * @param string$model The model name
     * @return string
     */
    public function getControllerName($modelName)
    {
        return studly_case(str_singular($modelName)) . 'Controller';
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
