<?php

namespace Bluecode\Generator\Tests\Commands;

use Artisan;
use DB;
use File;
use Bluecode\Generator\Tests\TestCase;

class PackageGeneratorCommandTest extends TestCase
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
     * @group package
     */
    public function test_create_package()
    {
        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--no-interaction' => true
        ]);

        $this->assertDirectoryExists($this->outputPath . '/sample_vendor/sample_package');

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/composer.json',
            $this->outputPath . '/sample_vendor/sample_package/composer.json'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/SamplePackagePackageProvider_plain.php',
            $this->outputPath . '/sample_vendor/sample_package/src/SamplePackagePackageProvider.php'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_with_model()
    {
        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--model' => 'Bar',
            '--no-interaction' => true
        ]);

        $this->assertDirectoryExists($this->outputPath . '/sample_vendor/sample_package');

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/composer.json',
            $this->outputPath . '/sample_vendor/sample_package/composer.json'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/SamplePackagePackageProvider.php',
            $this->outputPath . '/sample_vendor/sample_package/src/SamplePackagePackageProvider.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/routes.php',
            $this->outputPath . '/sample_vendor/sample_package/src/routes.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/Models/Bar.php',
            $this->outputPath . '/sample_vendor/sample_package/src/Models/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/Http/Controllers/BarController.php',
            $this->outputPath . '/sample_vendor/sample_package/src/Http/Controllers/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/index.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/table.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/table.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/create.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/create.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/edit.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/edit.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/form.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/form.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/show.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/show.blade.php'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_with_model_and_some_actions()
    {
        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--model' => 'Bar',
            '--actions' => 'index',
            '--no-interaction' => true
        ]);

        $this->assertDirectoryExists($this->outputPath . '/sample_vendor/sample_package');

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/composer.json',
            $this->outputPath . '/sample_vendor/sample_package/composer.json'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/SamplePackagePackageProvider.php',
            $this->outputPath . '/sample_vendor/sample_package/src/SamplePackagePackageProvider.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/routes.php',
            $this->outputPath . '/sample_vendor/sample_package/src/routes.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/Models/Bar.php',
            $this->outputPath . '/sample_vendor/sample_package/src/Models/Bar.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/Http/Controllers/BarController.php',
            $this->outputPath . '/sample_vendor/sample_package/src/Http/Controllers/BarController.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/index.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/index.blade.php'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/resources/views/bars/table.blade.php',
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/table.blade.php'
        );

        $this->assertFileNotExists(
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/create.blade.php'
        );

        $this->assertFileNotExists(
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/edit.blade.php'
        );

        $this->assertFileNotExists(
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/form.blade.php'
        );

        $this->assertFileNotExists(
            $this->outputPath . '/sample_vendor/sample_package/src/resources/views/bars/show.blade.php'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_with_custom_path()
    {
        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--path' => 'vendor/module',
            '--no-interaction' => true
        ]);

        $this->assertDirectoryExists($this->outputPath . '/vendor/module');

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/composer.json',
            $this->outputPath . '/vendor/module/composer.json'
        );

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/SamplePackagePackageProvider_plain.php',
            $this->outputPath . '/vendor/module/src/SamplePackagePackageProvider.php'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_and_not_update_composer_file()
    {
        File::makeDirectory($this->outputPath . '/sample_vendor/sample_package', '0755', true);
        File::put($this->outputPath . '/sample_vendor/sample_package/composer.json', 'Package composer');

        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--no-interaction' => true
        ]);

        $this->assertStringEqualsFile(
            $this->outputPath . '/sample_vendor/sample_package/composer.json',
            'Package composer'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_and_append_into_route_file()
    {
        File::makeDirectory($this->outputPath . '/sample_vendor/sample_package/src', '0755', true);
        File::put($this->outputPath . '/sample_vendor/sample_package/src/routes.php', "<?php\n");

        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--model' => 'Bar',
            '--no-interaction' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/routes.php',
            $this->outputPath . '/sample_vendor/sample_package/src/routes.php'
        );
    }

    /**
     * @group package
     */
    public function test_create_package_and_not_update_route_file()
    {
        File::makeDirectory($this->outputPath . '/sample_vendor/sample_package/src', '0755', true);
        File::copy(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/routes.php',
            $this->outputPath . '/sample_vendor/sample_package/src/routes.php'
        );

        $this->artisan('gen:package', [
            'vendor' => 'SampleVendor',
            'package' => 'SamplePackage',
            '--model' => 'Bar',
            '--no-interaction' => true
        ]);

        $this->assertFileEquals(
            $this->expectedPath . '/packages/sample_vendor/sample_package/src/routes.php',
            $this->outputPath . '/sample_vendor/sample_package/src/routes.php'
        );
    }
}
