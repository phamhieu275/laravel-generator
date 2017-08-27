<?php

namespace Bluecode\Generator\Tests\Commands;

use Bluecode\Generator\Tests\TestCase;

class MvcGeneratorCommandTest extends TestCase
{
    /**
     * @group mvc
     */
    public function test_create_basic_mvc()
    {
        $this->artisan('gen:mvc', [
            'model' => 'Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            $this->outputPath . '/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/index.blade.php',
            $this->outputPath . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/table.blade.php',
            $this->outputPath . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/form.blade.php',
            $this->outputPath . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/create.blade.php',
            $this->outputPath . '/foos/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/edit.blade.php',
            $this->outputPath . '/foos/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/show.blade.php',
            $this->outputPath . '/foos/show.blade.php'
        );
    }

    /**
     * @group mvc
     */
    public function test_create_mvc_only_some_actions()
    {
        $this->artisan('gen:mvc', [
            'model' => 'Foo',
            '--actions' => 'index,create'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            $this->outputPath . '/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/index.blade.php',
            $this->outputPath . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/table.blade.php',
            $this->outputPath . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/form.blade.php',
            $this->outputPath . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/create.blade.php',
            $this->outputPath . '/foos/create.blade.php'
        );

        $this->assertFileNotExists($this->outputPath . '/foos/edit.blade.php');
        $this->assertFileNotExists($this->outputPath . '/foos/show.blade.php');
    }
}
