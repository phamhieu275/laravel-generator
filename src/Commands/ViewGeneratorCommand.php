<?php

namespace Bluecode\Generator\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;
use Bluecode\Generator\Traits\TemplateTrait;
use Bluecode\Generator\Traits\GeneratorCommandTrait;
use Bluecode\Generator\Parser\SchemaParser;

class ViewGeneratorCommand extends GeneratorCommand
{
    use TemplateTrait;
    use GeneratorCommandTrait;

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:view
        {name : The name of the view}
        {model : The name of the model}
        {--f|force : Force overwriting existing files}
        {--p|path= : The location where the view file should be created}
        {--pk|package= : The package name}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view file';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'View';

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
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->getTemplatePath() . '/views/' . $this->argument('name') . '.blade.stub';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->argument('name');

        if (! $this->files->exists($this->getStub())) {
            $this->info("The template of the {$name} view is not found.");
            return false;
        }

        $this->type = "View {$name}";

        if (is_callable('parent::handle')) {
            parent::handle();
        } else {
            parent::fire();
        }
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        return $name;
    }

    /**
     * Get the destination class path.
     *
     * @param string $modelName
     * @return string
     */
    protected function getPath($name)
    {
        if ($this->option('path')) {
            $basePath = trim($this->option('path'));
        } else {
            $viewFolderName = $this->getViewNamespace($this->argument('model'));
            $basePath = config('generator.path.view') . '/' . $viewFolderName;
        }

        return $basePath . '/' . "{$name}.blade.php";
    }

    /**
     * Get the replaces.
     *
     * @param <type> $modelName The model name
     * @return array
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $modelName = $this->argument('model');

        $replaces = [
            'DummyMainLayout' => config('generator.view.layout'),
            'DummyRoutePrefix' => $this->getRoutePrefix($modelName, $this->option('package')),
            'DummyPaginator' => str_plural(lcfirst($modelName)),
            'DummyModelVariable' => camel_case($modelName),
            'DummyViewNamespace' => $this->getViewNamespace($modelName, $this->option('package')),
            'DummyTableHead' => '',
            'DummyTableBody' => '',
            'DummyFormInputs' => '',
            'DummyShowFields' => ''
        ];

        $tableName = str_plural(snake_case($modelName));
        $fields = $this->schemaParser->getFillableColumns($tableName);

        if (! $fields->isEmpty()) {
            switch ($name) {
                case 'table':
                    $replaces = $this->buildTableView($replaces, $fields);
                    break;
                case 'form':
                    $replaces['DummyFormInputs'] = $this->buildFormInputs($fields);
                    break;
                case 'show':
                    $replaces['DummyShowFields'] = $this->buildShowFields($fields, camel_case($modelName));
                    break;
            }
        }

        return str_replace(array_keys($replaces), array_values($replaces), $stub);
    }

    /**
     * Build a table view.
     *
     * @param array $replaces The replaces
     * @param array $fields The fields
     * @return array
     */
    private function buildTableView($replaces, $fields)
    {
        $headerColumns = $bodyColumns = [];
        foreach ($fields->keys()->all() as $field) {
            $headerColumns[] = '<th>' . title_case(str_replace('_', ' ', $field)) . '</th>';
            $bodyColumns[] = '<td>{!! $' . $replaces['DummyModelVariable'] . '->' . $field . ' !!}</td>';
        }

        $glue = "\n" . str_repeat(' ', 12);
        $replaces['DummyTableHead'] = $glue. implode($glue, $headerColumns);
        $replaces['DummyTableBody'] = $glue . implode($glue, $bodyColumns);

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
        $fieldTemplate = $this->files->get($this->getTemplatePath() . '/views/form_field.blade.stub');

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

        return implode("\n", $inputs);
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
        $fieldTemplate = $this->files->get($this->getTemplatePath() . '/views/show_field.blade.stub');

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

        return implode("\n", $showFields);
    }
}
