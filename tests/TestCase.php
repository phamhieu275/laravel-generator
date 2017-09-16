<?php

namespace Bluecode\Generator\Tests;

use File;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;

class TestCase extends BaseTestCase
{
    /**
     * the expected directory path to compare
     *
     * @var string
     */
    public $expectedPath = __DIR__ . DIRECTORY_SEPARATOR . 'expected';

    /**
     * file is created into the output path
     *
     * @var string
     */
    public $outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'output' . DIRECTORY_SEPARATOR;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'output');

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        if (! File::exists($this->outputPath)) {
            File::makeDirectory($this->outputPath);
        }

        File::copy(
            __DIR__ . DIRECTORY_SEPARATOR . 'composer.json',
            $this->outputPath . DIRECTORY_SEPARATOR . 'composer.json'
        );

        File::makeDirectory(app_path());
        File::makeDirectory(database_path('migrations'), '0755', true);
        File::makeDirectory(resource_path('views'), '0755', true);
    }

    public function tearDown()
    {
        parent::tearDown();

        File::cleanDirectory($this->outputPath);
    }
}
