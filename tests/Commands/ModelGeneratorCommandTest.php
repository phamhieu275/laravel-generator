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
            'name' => 'Models\Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            app_path() . '/Models/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_table_name()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Foo',
            '--table' => 'bar'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_table_name.php',
            app_path() . '/Models/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_fillable()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Foo',
            '--fillable' => 'name,content'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_custom_fillable.php',
            app_path() . '/Models/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_use_softdelete()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Foo',
            '--softDelete' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo_use_softdelete.php',
            app_path() . '/Models/Foo.php'
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
            app_path() . '/Sample/Models/Foo.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_with_existed_table()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            app_path() . '/Models/Bar.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_model_and_migration()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Bar',
            '--migration' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            app_path() . '/Models/Bar.php'
        );

        preg_match('/[0-9_]*_create_bars_table/', Artisan::output(), $matches);

        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bars_table.php',
            database_path('migrations') . "/{$matches[0]}.php"
        );
    }

    /**
     * @group model
     */
    public function test_create_model_and_controller()
    {
        $this->artisan('gen:model', [
            'name' => 'Models\Bar',
            '--controller' => true,
            '--resource' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Bar.php',
            app_path() . '/Models/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/BarController.php',
            app_path() . '/Http/Controllers/BarController.php'
        );
    }

    /**
     * @group model
     */
    public function test_create_basic_model_with_overwrite_option()
    {
        File::makeDirectory(app_path() . '/Models');
        File::put(app_path() . '/Models/Foo.php', 'abc');
        $this->artisan('gen:model', [
            'name' => 'Models\Foo',
            '--overwrite' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            app_path() . '/Models/Foo.php'
        );
    }
}
