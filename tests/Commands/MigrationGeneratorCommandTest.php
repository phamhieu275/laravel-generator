<?php

namespace Bluecode\Generator\Tests\Commands;

use DB;
use File;
use Artisan;
use Bluecode\Generator\Tests\TestCase;

class MigrationGeneratorCommandTest extends TestCase
{
    /**
     * @group migration
     */
    public function test_create_migration_from_existed_table()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_bars_table.sql'));

        $this->artisan('gen:migration', [
            'name' => 'create_bars_table',
            '--create' => 'bars'
        ]);

        $output = Artisan::output();
        $filename = str_replace('Created Migration: ', '', str_replace("\n", '', $output));

        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_bars_table.php',
            database_path('migrations') . "/{$filename}.php"
        );

        DB::statement('DROP TABLE IF EXISTS `bars`');
    }

    /**
     * @group migration
     */
    public function test_create_migration_for_special_column()
    {
        DB::unprepared(File::get(__DIR__ . '/../sql/create_hoges_table.sql'));

        $this->artisan('gen:migration', [
            'name' => 'create_hoges_table',
            '--create' => 'hoges'
        ]);

        $output = Artisan::output();
        $filename = str_replace('Created Migration: ', '', str_replace("\n", '', $output));

        $this->assertFileEquals(
            $this->expectedPath . '/migrations/create_hoges_table.php',
            database_path('migrations') . "/{$filename}.php"
        );

        DB::statement('DROP TABLE IF EXISTS `hoges`');
    }
}
