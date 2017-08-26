<?php

namespace Bluecode\Generator\Tests\Commands;

use Bluecode\Generator\Tests\TestCase;

class ModelGeneratorCommandTest extends TestCase
{
    /**
     * @group model
     */
    public function test_basic_create_model()
    {
        $this->artisan('gen:model', [
            'name' => 'Foo',
        ]);

        $this->assertFileEquals($this->expectPath . '/Foo.php', $this->outputPath . '/Foo.php');
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

        $this->assertFileEquals($this->expectPath . '/Foo_table_bar.php', $this->outputPath . '/Foo.php');
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

        $this->assertFileEquals($this->expectPath . '/Foo_has_fillable.php', $this->outputPath . '/Foo.php');
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

        $this->assertFileEquals($this->expectPath . '/Foo_use_softdelete.php', $this->outputPath . '/Foo.php');
    }
}
