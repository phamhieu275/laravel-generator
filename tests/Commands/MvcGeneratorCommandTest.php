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
            'model' => 'Models/Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            app_path() . '/Models/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            app_path() . '/Http/Controllers/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/index.blade.php',
            resource_path('views') . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/table.blade.php',
            resource_path('views') . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/form.blade.php',
            resource_path('views') . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/create.blade.php',
            resource_path('views') . '/foos/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/edit.blade.php',
            resource_path('views') . '/foos/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/show.blade.php',
            resource_path('views') . '/foos/show.blade.php'
        );
    }

    /**
     * @group mvc
     */
    public function test_create_mvc_only_some_actions()
    {
        $this->artisan('gen:mvc', [
            'model' => 'Models/Foo',
            '--actions' => 'index,create'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            app_path() . '/Models/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            app_path() . '/Http/Controllers/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/index.blade.php',
            resource_path('views') . '/foos/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/table.blade.php',
            resource_path('views') . '/foos/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/form.blade.php',
            resource_path('views') . '/foos/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/foos/create.blade.php',
            resource_path('views') . '/foos/create.blade.php'
        );

        $this->assertFileNotExists(resource_path('views') . '/foos/edit.blade.php');
        $this->assertFileNotExists(resource_path('views') . '/foos/show.blade.php');
    }

    /**
     * @group mvc
     */
    public function test_create_mvc_with_existed_table()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));

        $this->artisan('gen:mvc', [
            'model' => 'Models/Bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            app_path() . '/Models/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            app_path() . '/Http/Controllers/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/index.blade.php',
            resource_path('views') . '/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/table.blade.php',
            resource_path('views') . '/bars/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/form.blade.php',
            resource_path('views') . '/bars/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/create.blade.php',
            resource_path('views') . '/bars/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/edit.blade.php',
            resource_path('views') . '/bars/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/views/bars/show.blade.php',
            resource_path('views') . '/bars/show.blade.php'
        );

        DB::statement('DROP TABLE IF EXISTS `bars`');
    }
}
