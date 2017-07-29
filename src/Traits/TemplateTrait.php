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
}
