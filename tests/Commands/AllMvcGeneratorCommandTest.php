<?php

namespace Bluecode\Generator\Tests\Commands;

use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class AllMvcGeneratorCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));
    }

    public function tearDown()
    {
        DB::statement('DROP TABLE IF EXISTS `bars`');

        parent::tearDown();
    }

    /**
     * @group all-mvc
     */
    public function test_create_many_mvc()
    {
        $this->artisan('gen:all:mvc');

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            $this->outputPath . '/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/index.blade.php',
            $this->outputPath . '/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/table.blade.php',
            $this->outputPath . '/bars/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/form.blade.php',
            $this->outputPath . '/bars/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/create.blade.php',
            $this->outputPath . '/bars/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/edit.blade.php',
            $this->outputPath . '/bars/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/show.blade.php',
            $this->outputPath . '/bars/show.blade.php'
        );
    }

    /**
     * @group all-mvc
     */
    public function test_create_many_mvc_with_only_option()
    {
        $this->artisan('gen:all:mvc', [
            '--only' => 'Foo,Bar'
        ]);

        // Foo Mvc
        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            $this->outputPath . '/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/index.blade.php',
            $this->outputPath . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/table.blade.php',
            $this->outputPath . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/form.blade.php',
            $this->outputPath . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/create.blade.php',
            $this->outputPath . '/foos/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/edit.blade.php',
            $this->outputPath . '/foos/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/show.blade.php',
            $this->outputPath . '/foos/show.blade.php'
        );

        // Bar Mvc
        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            $this->outputPath . '/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/index.blade.php',
            $this->outputPath . '/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/table.blade.php',
            $this->outputPath . '/bars/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/form.blade.php',
            $this->outputPath . '/bars/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/create.blade.php',
            $this->outputPath . '/bars/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/edit.blade.php',
            $this->outputPath . '/bars/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/show.blade.php',
            $this->outputPath . '/bars/show.blade.php'
        );
    }

    /**
     * @group all-mvc
     */
    public function test_create_all_mvc_with_only_some_actions()
    {
        $this->artisan('gen:all:mvc', [
            '--only' => 'Foo,Bar',
            '--actions' => 'index,create'
        ]);

        // Foo Mvc
        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            $this->outputPath . '/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/index.blade.php',
            $this->outputPath . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/table.blade.php',
            $this->outputPath . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/form.blade.php',
            $this->outputPath . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/create.blade.php',
            $this->outputPath . '/foos/create.blade.php'
        );

        $this->assertFileNotExists($this->outputPath . '/foos/edit.blade.php');
        $this->assertFileNotExists($this->outputPath . '/foos/show.blade.php');

        // Bar Mvc
        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            $this->outputPath . '/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/index.blade.php',
            $this->outputPath . '/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/table.blade.php',
            $this->outputPath . '/bars/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/form.blade.php',
            $this->outputPath . '/bars/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/create.blade.php',
            $this->outputPath . '/bars/create.blade.php'
        );

        $this->assertFileNotExists($this->outputPath . '/bars/edit.blade.php');
        $this->assertFileNotExists($this->outputPath . '/bars/show.blade.php');
    }
}
