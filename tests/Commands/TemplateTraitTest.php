<?php

namespace Bluecode\Generator\Tests\Commands;

use Config;
use File;
use Bluecode\Generator\Tests\TestCase;
use Bluecode\Generator\Traits\TemplateTrait;

class TemplateTraitTest extends TestCase
{
    use TemplateTrait;

    /**
     * @group template
     */
    public function test_change_config_of_template_path()
    {
        File::makeDirectory(base_path('foo'));
        Config::set('generator.path.template', base_path('foo'));
        $this->assertEquals(base_path('foo'), $this->getTemplatePath());
    }
}
