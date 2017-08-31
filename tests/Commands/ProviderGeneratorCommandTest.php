<?php

namespace Bluecode\Generator\Tests\Commands;

use Bluecode\Generator\Tests\TestCase;

class ProviderGeneratorCommandTest extends TestCase
{
    /**
     * @group provider
     */
    public function test_create_provider()
    {
        $this->artisan('gen:provider', [
            'name' => 'FooProvider',
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/providers/FooProvider.php',
            $this->outputPath . '/FooProvider.php'
        );
    }
}
