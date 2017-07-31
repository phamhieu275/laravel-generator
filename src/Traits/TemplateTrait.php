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
        $templatePath = config('generator.path_public_template');
        if (! is_dir($templatePath)) {
            $templatePath = __DIR__ . '/../../templates/';
        }

        return $templatePath;
    }

    /**
     * Get the view namespace.
     *
     * @param string $rootNamespace The root namespace
     * @return string
     */
    public function getViewNamespace($rootNamespace)
    {
        return collect(explode('\\', $rootNamespace))
            ->map(function ($name) {
                return snake_case($name);
            })
            ->flatten()
            ->implode('.');
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
    public function getResourceName($modelName, $prefix = '')
    {
        $resourceUrl = str_plural(snake_case($modelName));
        if (! empty($prefix)) {
            return snake_case($prefix) . '.' . $resourceUrl;
        }

        return $resourceUrl;
    }
}
