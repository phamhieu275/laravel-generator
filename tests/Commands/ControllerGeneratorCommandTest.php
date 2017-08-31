<?php

namespace Bluecode\Generator\Tests\Commands;

use Bluecode\Generator\Tests\TestCase;

class ControllerGeneratorCommandTest extends TestCase
{
    /**
     * @group controller
     */
    public function test_create_controller_with_model()
    {
        $this->artisan('gen:controller', [
            'name' => 'FooController',
            '--model' => 'Foo',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController.php',
            $this->outputPath . '/FooController.php'
        );
    }

    /**
     * @group controller
     */
    public function test_create_controller_with_namespace()
    {
        $this->artisan('gen:controller', [
            'name' => 'FooController',
            '--model' => 'Foo',
            '--namespace' => 'App\Http\Controllers\Bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController_custom_namespace.php',
            $this->outputPath . '/FooController.php'
        );
    }

    /**
     * @group controller
     */
    public function test_create_controller_with_custom_root_namespace()
    {
        $this->artisan('gen:controller', [
            'name' => 'FooController',
            '--model' => 'Foo',
            '--rootNamespace' => 'Test\Sample',
            '--namespace' => 'Test\Sample\Http\Controllers',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController_custom_root_namespace.php',
            $this->outputPath . '/FooController.php'
        );
    }

    /**
     * @group controller
     */
    public function test_create_controller_route_prefix()
    {
        $this->artisan('gen:controller', [
            'name' => 'FooController',
            '--model' => 'Foo',
            '--routePrefix' => 'bar',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController_route_prefix.php',
            $this->outputPath . '/FooController.php'
        );
    }

    /**
     * @group controller
     */
    public function test_create_plain_controller()
    {
        $this->artisan('gen:controller', [
            'name' => 'FooController'
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/controllers/FooController_plain.php',
            $this->outputPath . '/FooController.php'
        );
    }
}
