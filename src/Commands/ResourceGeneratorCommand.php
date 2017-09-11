<?php

namespace Bluecode\Generator\Commands;

use Schema;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Symfony\Component\Console\Input\InputOption;

use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\GeneratorCommandTrait;

class ResourceGeneratorCommand extends ResourceMakeCommand
{
    use TemplateTrait;
    use GeneratorCommandTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:resource';

    /**
     * The content of toArray function
     *
     * @var string
     */
    protected $placeholder = 'parent::toArray($request)';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getTemplatePath() . '/' . basename(parent::getStub());
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        if ($this->option('table')) {
            return $this->buildListAttribute($stub, trim($this->option('table')));
        }

        return $stub;
    }

    /**
     * Build the content of toArray function from a given table
     *
     * @param string $stub The stub
     * @param string $table The table
     * @return string
     */
    private function buildListAttribute($stub, $table)
    {
        $attributes = Schema::getColumnListing($table);

        if (empty($attributes)) {
            return $stub;
        }

        $attributes = collect($attributes)
            ->map(function ($attribute) {
                return  sprintf('%s%s => $this->%s,', str_repeat(' ', 12), "'{$attribute}'", $attribute);
            })
            ->implode("\n");

        $replace = "[\n" . $attributes . "\n" . str_repeat(' ', 8) . "]";

        return str_replace($this->placeholder, $replace, $stub);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array_merge(parent::getOptions(), [
            ['overwrite', null, InputOption::VALUE_NONE, 'Force overwriting existing files'],

            ['table', null, InputOption::VALUE_OPTIONAL, 'Generate the resource for the given table'],
        ]);
    }
}
