<?php namespace Bluecode\Generator\Generators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command;
use Bluecode\Generator\Parser\SchemaParser;

class BaseGenerator
{
    /**
     * The object of command
     *
     * @var object
     */
    protected $command;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $fileHelper;

    /**
     * The parser class.
     *
     * @var \Bluecode\Generator\Parser\SchemaParser
     */
    public $schemaParser;

    /**
     * The information of fillable columns in a table
     *
     * @var array
     */
    public $fillableColumns = [];

    /**
     * The type of class being generated.
     *
     * @var string
     */
    public $type;

    /**
     * The template path.
     *
     * @var string
     */
    public $templatePath;

    /**
     * The root path of class being generated.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * The template Data.
     *
     * @var array
     */
    public $templateData;

    /**
     * Create a new genrator instance.
     *
     * @return void
     */
    public function __construct($command)
    {
        $this->command = $command;

        $this->fileHelper = new Filesystem;

        $this->templatePath = $this->getTemplatePath();
        $this->type = $this->getType();

        $this->rootPath = config('generator.path_'.$this->type);
    }

    /**
     * Set path for template
     *
     * @param void
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function generateFile($filename, $templateData, $templatePath = null)
    {
        $this->makeDirectory($this->rootPath);

        $path = $this->rootPath.$filename;

        if (is_null($templatePath)) {
            $templatePath = $this->templatePath;
        }
        $content = $this->generateContent($templatePath, $templateData);

        $this->fileHelper->put($path, $content);

        $this->command->info($filename.' created successfully.');
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->fileHelper->exists($path)) {
            $this->fileHelper->makeDirectory($path, 0777, true, true);
        }
    }

    /**
     * Generate content with template data is given.
     *
     * @param  array  $templateData
     * @return string
     */
    protected function generateContent($templatePath, $templateData)
    {
        $template = $this->getTemplate($templatePath);

        $content = $this->compile($template, $templateData);

        return $content;
    }

    /**
     * Get the template for the generator.
     *
     * @param  string $templatePath
     * @return string
     */
    protected function getTemplate($templatePath)
    {
        $path = base_path('resources/generator-templates/'.$templatePath.'.stub');

        if (!file_exists($path)) {
            $path = __DIR__.'/../../templates/'.$templatePath.'.stub';
        }

        return $this->fileHelper->get($path);
    }

    /**
     * Compile the template using
     * the given data
     *
     * @param $template
     * @param $data
     * @return mixed
     */
    public function compile($template, $data)
    {
        foreach ($data as $key => $value) {
            $template = preg_replace("/\\$$key\\$/i", $value, $template);
        }
        return $template;
    }
}
