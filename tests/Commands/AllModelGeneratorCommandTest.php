<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class AllModelGeneratorCommandTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bazs_table.sql'));
    }

    public function tearDown()
    {
        DB::statement('DROP TABLE IF EXISTS `bars`');
        DB::statement('DROP TABLE IF EXISTS `bazs`');

        parent::tearDown();
    }

    /**
     * @group all-model
     */
    public function test_create_many_model()
    {
        $this->artisan('gen:all:model');

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/models/Baz.php',
            $this->outputPath . '/Baz.php'
        );
    }

    /**
     * @group all-model
     */
    public function test_create_many_model_with_only_option()
    {
        $this->artisan('gen:all:model', [
            '--only' => 'Foo,Bar'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );
    }

    /**
     * @group all-model
     */
    public function test_create_many_model_with_exclude_option()
    {
        $this->artisan('gen:all:model', [
            '--exclude' => 'Bar'
        ]);

        $this->assertFileNotExists($this->outputPath . '/Bar.php');

        $this->assertFileEquals(
            $this->expectedPath . '/models/Baz.php',
            $this->outputPath . '/Baz.php'
        );
    }
}
