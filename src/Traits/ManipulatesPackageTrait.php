<?php

namespace Bluecode\Generator\Traits;

use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Exception\RuntimeException;

trait ManipulatesPackageTrait
{
    /**
     * Create package folder.
     *
     * @param string $packagePath
     *
     * @throws RuntimeException
     */
    protected function createPackageFolder($packagePath)
    {
        $this->info('Create package folder.');

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

    /**
     * Remove package folder.
     *
     * @param $packagePath
     * @throws RuntimeException
     * @return void
     */
    protected function removePackageFolder($packagePath)
    {
        $this->info('Remove package folder.');

        if (File::exists($packagePath)) {
            if (! File::deleteDirectory($packagePath)) {
                throw new RuntimeException('Cannot remove package folder');
            }

            $this->info('Package folder removed successfully.');
        } else {
            $this->info('Package folder does not exists. Skipping.');
        }
    }

    /**
     * Creates a composer file.
     *
     * @param string $templatePath The template path
     * @param string $packagePath The package path
     * @param string $packageName The package name
     * @return void
     */
    protected function createComposerFile($templatePath, $packagePath, $packageName)
    {
        $path = $templatePath . '/package/composer.json.stub';

        $content = str_replace('DummyPackageName', $packageName, File::get($path));

        File::put($packagePath . '/composer.json', $content);

        $this->info('composer.json created successfully.');
    }

    /**
     * Creates a route file.
     *
     * @param string $packagePath The package path
     * @return void
     */
    protected function createRouteFile($packagePath)
    {
        if (File::exists($packagePath . '/src/route.php')) {
            return;
        }

        File::put($packagePath . '/src/route.php', '');

        $this->info('route.php created successfully.');
    }
}
