<?php

namespace Bluecode\Generator\Tests\Commands;

use DB;
use File;
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
    }

    /**
     * @group mvc1
     */
    public function test_create_mvc_with_existed_table()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));

        $this->artisan('gen:mvc', [
            'model' => 'Bar',
        ]);

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

        DB::statement('DROP TABLE IF EXISTS `bars`');
    }
}
