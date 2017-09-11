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
        if ($this->hasOption('path')
            && $this->option('path')
            && $this->hasOption('package')
            && $this->option('package')) {
            $packagePath = config('generator.path.package');

            return $packagePath . '/' . trim($this->option('path'), '/') . '/' . class_basename($name) . '.php';
        }

        return parent::getPath($name);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    public function getDefaultNamespace($rootNamespace)
    {
        if ($this->hasOption('namespace') && $this->option('namespace')) {
            return trim($this->option('namespace'));
        }

        return parent::getDefaultNamespace($rootNamespace);
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    public function rootNamespace()
    {
        if ($this->hasOption('rootNamespace') && $this->option('rootNamespace')) {
            return $this->option('rootNamespace');
        }

        return parent::rootNamespace();
    }
}
