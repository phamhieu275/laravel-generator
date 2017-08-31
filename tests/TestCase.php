<?php

namespace Bluecode\Generator\Tests;

use Config;
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
    public $outputPath = __DIR__ . DIRECTORY_SEPARATOR . 'output';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        if (! File::exists($this->outputPath)) {
            File::makeDirectory($this->outputPath);
        }

        $this->setConfig();
    }

    public function tearDown()
    {
        parent::tearDown();

        File::cleanDirectory($this->outputPath);
    }

    /**
     * Set the temporary configuration
     *
     * @return void
     */
    public function setConfig()
    {
        Config::set('generator.path.migration', $this->outputPath);
        Config::set('generator.path.model', $this->outputPath);
        Config::set('generator.path.controller', $this->outputPath);
        Config::set('generator.path.view', $this->outputPath);
        Config::set('generator.path.provider', $this->outputPath);
        Config::set('generator.path.package', $this->outputPath);
    }
}
