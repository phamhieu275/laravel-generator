<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use Bluecode\Generator\Tests\TestCase;

class ViewGeneratorCommandTest extends TestCase
{
    /**
     * @group view
     */
    public function test_create_print_view()
    {
        $this->artisan('gen:view', [
            'name' => 'print',
            'model' => 'Foo'
        ]);

        $this->assertEquals(Artisan::output(), "The template of the print view is not found.\n");
    }
}
