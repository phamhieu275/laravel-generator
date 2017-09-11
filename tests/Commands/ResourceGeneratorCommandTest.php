<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class ResourceGeneratorCommandTest extends TestCase
{
    /**
     * @group resource
     */
    public function test_create_resource()
    {
        $this->artisan('gen:resource', [
            'name' => 'Foo'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/resources/Foo.php',
            app_path() . '/Http/Resources/Foo.php'
        );
    }

    /**
     * @group resource
     */
    public function test_create_resource_collection()
    {
        $this->artisan('gen:resource', [
            'name' => 'FooCollection'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/resources/FooCollection.php',
            app_path() . '/Http/Resources/FooCollection.php'
        );
    }

    /**
     * @group resource
     */
    public function test_create_resource_with_existed_table()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));

        $this->artisan('gen:resource', [
            'name' => 'Bar',
            '--table' => 'bars',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/resources/Bar.php',
            app_path() . '/Http/Resources/Bar.php'
        );

        DB::statement('DROP TABLE IF EXISTS `bars`');
    }

    /**
     * @group resource
     */
    public function test_create_resource_with_not_existed_table()
    {
        $this->artisan('gen:resource', [
            'name' => 'Foo',
            '--table' => 'foos',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/resources/Foo.php',
            app_path() . '/Http/Resources/Foo.php'
        );
    }
}
