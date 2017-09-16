<?php

namespace Bluecode\Generator\Tests\Commands;

use Bluecode\Generator\Tests\TestCase;

class ApiGeneratorCommandTest extends TestCase
{
    /**
     * @group api
     */
    public function test_create_api_controller_and_resource()
    {
        $this->artisan('gen:api', [
            'model' => 'Models/Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/models/Foo.php',
            app_path() . '/Models/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController_api.php',
            app_path() . '/Http/Controllers/FooController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/resources/Foo.php',
            app_path() . '/Http/Resources/Foo.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/resources/FooCollection.php',
            app_path() . '/Http/Resources/FooCollection.php'
        );
    }
}
