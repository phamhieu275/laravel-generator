<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Parser\SchemaParser;

class ViewGeneratorCommand extends GeneratorCommand
{
    use TemplateTrait;

    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'gen:view';

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files, SchemaParser $schemaParser)
    {
        parent::__construct($files);

        $this->schemaParser = $schemaParser;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        return array_merge($options, [
            ['path', '', InputOption::VALUE_OPTIONAL, 'The location where the view file should be created.'],

            ['view', '', InputOption::VALUE_OPTIONAL, 'The view namespace'],

            ['package', '', InputOption::VALUE_OPTIONAL, 'The package name'],
        ]);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $templatePath = $this->getTemplatePath();
        return $templatePath . '/views/';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        $modelName = $this->argument('name');
        $viewPath = $this->getPath($modelName);
        if (! $this->files->isDirectory($viewPath)) {
            $this->files->makeDirectory($viewPath, 0777, true, true);
        }

        $templatePath = $this->getStub();

        $replaces = $this->getReplaces($modelName);

        $views = ['index', 'table', 'create', 'edit', 'show', 'form'];
        foreach ($views as $view) {
            $stub = $this->files->get($templatePath . "{$view}.blade.stub");

            $path = $viewPath . "/{$view}.blade.php";
            $this->files->put($path, str_replace(array_keys($replaces), array_values($replaces), $stub));

            $this->info("{$view}.blade.php is created successfully.");
        }
    }

    /**
     * Get the destination class path.
     *
     * @param string $modelName
     * @return string
     */
    protected function getPath($modelName)
    {
        if ($this->option('path')) {
            $basePath = trim($this->option('path'));
        } else {
            $basePath = config('generator.path.view');
        }

        return trim($basePath, '/') . '/' . str_plural(snake_case($modelName));
    }

    /**
     * get view folder path
     *
     * @param string $modelInput The model class
     * @return string
     */
    protected function getViewNamespace($modelClass)
    {
        $viewFolder = str_plural(snake_case(class_basename($modelClass)));

        if ($this->option('view')) {
            return $this->option('view') . $viewFolder;
        }

        return $viewFolder;
    }

    /**
     * Get the replaces.
     *
     * @param <type> $modelName The model name
     * @return array
     */
    protected function getReplaces($modelName)
    {
        $replaces = [
            'DummyMainLayout' => config('generator.view.layout'),
            'DummyRoutePrefix' => $this->getRoutePrefix($modelName, $this->option('package')),
            'DummyPaginator' => str_plural(lcfirst($modelName)),
            'DummyModelVariable' => camel_case($modelName),
            'DummyViewNamespace' => $this->getViewNamespace($modelName),
            'DummyTableHead' => '',
            'DummyTableBody' => '',
            'DummyFormInputs' => '',
            'DummyShowFields' => ''
        ];

        $tableName = str_plural(snake_case($modelName));
        $fields = $this->schemaParser->getFillableFields($tableName);

        if ($fields->isEmpty()) {
            return $replaces;
        }

        $headerColumns = $bodyColumns = [];
        foreach ($fields->keys()->all() as $field) {
            $headerColumns[] = '<th>' . title_case(str_replace('_', ' ', $field)) . '</th>';
            $bodyColumns[] = '<td>{!! $' . $replaces['DummyModelVariable'] . '->' . $field . ' !!}</td>';
        }

        $glue = "\n" . str_repeat(' ', 16);
        $replaces['DummyTableHead'] = implode($glue, $headerColumns);
        $replaces['DummyTableBody'] = implode($glue, $bodyColumns);

        $replaces['DummyFormInputs'] = $this->buildFormInputs($fields);

        $replaces['DummyShowFields'] = $this->buildShowFields($fields, camel_case($modelName));

        return $replaces;
    }

    /**
     * Build form inputs.
     *
     * @param \Illuminate\Support\Collection $fields The fields
     * @return string
     */
    private function buildFormInputs($fields)
    {
        $fieldTemplate = $this->files->get($this->getStub() . 'form_field.blade.stub');

        $inputs = [];
        $placeholders = [
            'DummyFieldName',
            'DummyInputLabel',
            'DummyInputType',
        ];
        foreach ($fields as $field) {
            switch ($field->getType()->getName()) {
                case 'integer':
                    $inputType = 'number';
                    break;
                case 'text':
                    $inputType = 'textarea';
                    break;
                case 'date':
                    $inputType = 'date';
                    break;
                case 'boolean':
                    $inputType = 'checkbox';
                    break;
                default:
                    $inputType = 'text';
                    break;
            }

            $replaces = [
                $field->getName(),
                title_case(str_replace('_', ' ', $field->getName())),
                $inputType
            ];

            $inputs[] = str_replace($placeholders, $replaces, $fieldTemplate);
        }

        return implode("\n\n", $inputs);
    }

    /**
     * Build show fields.
     *
     * @param \Illuminate\Support\Collection $fields The fields
     * @param string $modelVariable The model variable
     * @return string
     */
    private function buildShowFields($fields, $modelVariable)
    {
        $fieldTemplate = $this->files->get($this->getStub() . 'show_field.blade.stub');

        $placeholders = [
            'DummyFieldName',
            'DummyFieldLabel',
            'DummyModelVariable',
        ];

        $showFields = [];
        foreach ($fields as $field) {
            $replaces = [
                $field->getName(),
                ucfirst($field->getName()),
                $modelVariable
            ];

            $showFields[] = str_replace($placeholders, $replaces, $fieldTemplate);
        }

        return implode("\n\n", $showFields);
    }
}
