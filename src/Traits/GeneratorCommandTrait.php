<?php

namespace Bluecode\Generator\Traits;

trait GeneratorCommandTrait
{
    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    public function alreadyExists($rawName)
    {
        if ($this->hasOption('overwrite') && $this->option('overwrite')) {
            return false;
        }

        return parent::alreadyExists($rawName);
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    public function getPath($name)
    {
        if ($this->hasOption('path') && $this->option('path')) {
            if ($this->hasOption('package') && $this->option('package')) {
                $basePath = config('generator.path.package');
            } else {
                $basePath =  $this->laravel['path'];
            }
            
            return $basePath . '/'. trim($this->option('path')) . DIRECTORY_SEPARATOR . class_basename($name) . '.php';
        }

        $path = config('generator.path.' . strtolower($this->type));

        if ($path) {
            return $path . DIRECTORY_SEPARATOR . class_basename($name) . '.php';
        }

        return parent::getPath();
    }
}
