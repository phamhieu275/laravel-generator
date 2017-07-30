<?php

namespace Bluecode\Generator\Traits;

use Illuminate\Support\Str;

trait InteractsWithUserTrait
{
    /**
     * Get vendor part of the namespace part.
     *
     * @param string $default
     * @return string
     */
    protected function getVendor($default = '')
    {
        $vendor = $this->argument('vendor') ?: $default;

        return $this->askUser('The vendor name is a part of the namespace', $vendor);
    }

    /**
     * Get the name of package for the namespace.
     *
     * @param string $default
     * @return string
     */
    protected function getPackage($default = '')
    {
        $package = $this->argument('package') ?: $default;

        return $this->askUser('The name of package for the namespace', $package);
    }

    /**
     * Get vendor folder name.
     *
     * @param string $vendor
     * @return string
     */
    protected function getVendorFolderName($vendor)
    {
        $vendorFolderName = strtolower($vendor);

        return $this->askUser('The vendor folder name', $vendorFolderName);
    }

    /**
     * Get package folder name.
     *
     * @param string $package
     * @return string
     */
    protected function getPackageFolderName($package)
    {
        $packageFolderName = snake_case($package);

        return $this->askUser('The package folder name', $packageFolderName);
    }

    /**
     * Ask user.
     *
     * @param string $question The question
     * @param string $defaultValue The default value
     * @return string
     */
    protected function askUser($question, $defaultValue = '')
    {
        if ($this->option('interactive')) {
            return $this->ask($question, $defaultValue);
        }

        return $defaultValue;
    }
}
