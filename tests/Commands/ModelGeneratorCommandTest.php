<?php

namespace Bluecode\Generator\Tests\Commands;

use DB;
use File;
use Bluecode\Generator\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ModelGeneratorCommandTest extends TestCase
{
    /**
     * @group model
     */
    public function test_create_basic_model()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_table_name()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--table' => 'bar'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_table_name.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_fillable()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--fillable' => 'name,content'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_fillable.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_use_softdelete()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--softDelete' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_use_softdelete.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_namespace()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--namespace' => 'App\Bar\Models'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_namespace.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_custom_root_namespace()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--rootNamespace' => 'Test',
            '--namespace' => 'Test\Sample\Models'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_root_namespace.php',
            $this->outputPath . '/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_existed_table()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));

        $this->artisan('gen:model', [
            'name' => 'Bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        DB::statement('DROP TABLE IF EXISTS `bars`');
    }
}
