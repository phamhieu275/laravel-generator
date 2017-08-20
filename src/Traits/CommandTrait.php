<?php

namespace Bluecode\Generator\Traits;

trait CommandTrait
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
}
