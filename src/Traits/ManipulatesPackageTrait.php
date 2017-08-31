<?php

namespace Bluecode\Generator\Traits;

use File;
use Symfony\Component\Console\Exception\RuntimeException;

trait ManipulatesPackageTrait
{
    /**
     * Create package folder.
     *
     * @param string $packagePath
     * @throws RuntimeException
     */
    protected function createPackageFolder($packagePath)
    {
        if (File::exists($packagePath)) {
            $this->info('Package folder already exists. Skipping.');

            return;
        }

        if (! File::makeDirectory($packagePath, 0755, true)) {
            throw new RuntimeException('Cannot create package folder');
        }

        if (! File::exists($packagePath . '/src')) {
            File::makeDirectory($packagePath . '/src', 0755, true);
        }

        $this->info('Package folder created successfully.');
    }
}
