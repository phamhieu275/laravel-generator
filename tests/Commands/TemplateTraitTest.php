<?php

namespace Bluecode\Generator\Tests\Commands;

use Config;
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
        Config::set('generator.path.template', $this->outputPath);
        $this->assertEquals($this->outputPath, $this->getTemplatePath());
    }
}
