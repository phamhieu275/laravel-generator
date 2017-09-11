<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class AllMigrationGeneratorCommandTest extends TestCase
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
     * @group all-migration
     */
    public function test_create_many_migation()
    {
        $this->artisan('gen:all:migration');

        $output = Artisan::output();

        preg_match('/[0-9_]*_create_bars_table/', $output, $matches);
        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bars_table.php',
            database_path('migrations') . "//{$matches[0]}.php"
        );

        preg_match('/[0-9_]*_create_bazs_table/', $output, $matches);
        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bazs_table.php',
            database_path('migrations') . "//{$matches[0]}.php"
        );
    }

    /**
     * @group all-migration
     */
    public function test_create_many_migation_with_only_option()
    {
        $this->artisan('gen:all:migration', [
            '--only' => 'bars'
        ]);

        $output = Artisan::output();

        preg_match('/[0-9_]*_create_bars_table/', $output, $matches);
        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bars_table.php',
            database_path('migrations') . "//{$matches[0]}.php"
        );

        preg_match('/[0-9_]*_create_bazs_table/', $output, $matches);
        $this->assertCount(0, $matches);
    }

    /**
     * @group all-migration
     */
    public function test_create_many_migation_with_exclude_option()
    {
        $this->artisan('gen:all:migration', [
            '--exclude' => 'bars'
        ]);

        $output = Artisan::output();

        preg_match('/[0-9_]*_create_bars_table/', $output, $matches);
        $this->assertCount(0, $matches);

        preg_match('/[0-9_]*_create_bazs_table/', $output, $matches);
        $this->assertCount(1, $matches);
        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bazs_table.php',
            database_path('migrations') . "//{$matches[0]}.php"
        );
    }
}
