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
        if ($this->hasOption('force') && $this->option('force')) {
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
            return $this->option('path') . DIRECTORY_SEPARATOR . class_basename($name) . '.php';
        }

        $path = config('generator.path.' . strtolower($this->type));

        if ($path) {
            return $path . DIRECTORY_SEPARATOR . class_basename($name) . '.php';
        }

        return parent::getPath();
    }
}
