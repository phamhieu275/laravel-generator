<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class ModelGeneratorCommandTest extends TestCase
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
        $this->artisan('gen:model', [
            'name' => 'Bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_and_migration()
    {
        $this->artisan('gen:model', [
            'name' => 'Bar',
            '--migration' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        preg_match('/[0-9_]*_create_bars_table/', Artisan::output(), $matches);

        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bars_table.php',
            $this->outputPath . "/{$matches[0]}.php"
        );
    }

    /**
     * @group model
     */
    public function test_create_model_and_controller()
    {
        $this->artisan('gen:model', [
            'name' => 'Bar',
            '--controller' => true,
            '--resource' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            $this->outputPath . '/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            $this->outputPath . '/BarController.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_basic_model_with_force_option()
    {
        File::put($this->outputPath . '/Foo.php', 'abc');
        $this->artisan('gen:model', [
            'name' => 'Foo',
            '--force' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            $this->outputPath . '/Foo.php'
        );
    }
}
