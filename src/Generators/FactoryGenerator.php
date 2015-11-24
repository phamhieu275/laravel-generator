<?php namespace Bluecode\Generator\Generators;

use Bluecode\Generator\Parser\SchemaParser;
use Faker\Factory;

class FactoryGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * A list guard columns
     *
     * @var array
     */
    private $guardFields = ['created_at', 'updated_at', 'deleted_at', 'remember_token'];

    public function __construct($command)
    {
        parent::__construct($command);
        $this->schemaParser = new SchemaParser();
    }

    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'factory';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'common/Factory';
    }

    public function generate($data = [])
    {
        $templateData = $this->getTemplateData($data);

        $factoryContent = "\n\n".$this->generateContent($this->templatePath, $templateData);

        $this->command->info("\nUpdate factory for tables :".$data['TABLE_NAME']);

        $this->fileHelper->append($this->rootPath, $factoryContent);
    }

    /**
     * Fetch the template data
     *
     * @return array
     */
    public function getTemplateData($data = [])
    {
        // generate fields
        $fillableColumns = $this->schemaParser->getFillableFields($data['TABLE_NAME']);
        logger($fillableColumns);

        $faker = Factory::create();

        $fieldStr = [];
        foreach ($fillableColumns as $column) {
            try {
                if ($column['field'] === 'password') {
                    $func = 'bcrypt(str_random(10))';
                } else {
                    $faker->getFormatter($column['field']);

                    $func = '$faker->'.$column['field'];
                }
            } catch (\Exception $e) {
                logger($column['type']);
                switch ($column['type']) {
                    case 'tinyInteger':
                        $func = '$faker->randomNumber(2)';
                        break;
                    case 'smallInteger':
                        $func = '$faker->randomNumber(2)';
                        break;
                    case 'mediumInteger':
                        $func = '$faker->randomNumber(4)';
                        break;
                    case 'integer':
                        $func = '$faker->randomNumber(8)';
                        break;
                    case 'char':
                    case 'string':
                        logger($column['field']);
                        if (isset($column['args']) && !empty($column['args'])) {
                            $max = $column['args'];
                        } else {
                            $max = 100;
                        }
                        $func = "str_random($max)";
                        break;
                    case 'text':
                    case 'boolean':
                        $func = '$faker->'.$column['type'].'()';
                        break;
                    case 'date':
                        $func = '$faker->'.'dateTimeBetween()';
                        break;
                    default:
                        $func = '';
                }
            }

            if (!empty($func)) {
                $fieldStr[] = "'".$column['field']."' => ".$func;
            }
            
        }
        $data['FIELDS'] = implode(",\n\t\t", $fieldStr);

        return $data;
    }
}
