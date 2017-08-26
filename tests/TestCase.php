<?php

namespace Bluecode\Generator\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;
use Config;
use File;

class TestCase extends BaseTestCase
{
    /**
     * the expect directory path to compare
     *
     * @var string
     */
    public $expectPath = __DIR__ . DIRECTORY_SEPARATOR . 'expect';

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

        $this->setTempConfig();
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
    public function setTempConfig()
    {
        Config::set('generator.path.migration', $this->outputPath);
        Config::set('generator.path.model', $this->outputPath);
        Config::set('generator.path.controller', $this->outputPath);
        Config::set('generator.path.view', $this->outputPath);
    }
}
