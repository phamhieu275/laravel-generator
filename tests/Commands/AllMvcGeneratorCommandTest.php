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

        // Bar Mvc
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

        // Bar Mvc
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

        $this->assertFileNotExists(resource_path('views') . '/bars/edit.blade.php');
        $this->assertFileNotExists(resource_path('views') . '/bars/show.blade.php');
    }
}
