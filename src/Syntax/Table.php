<?php namespace Bluecode\Generator\Syntax;

/**
 * Class Table
 */
abstract class Table
{

    /**
     * @var string
     */
    protected $table;

    public function run($fields, $table)
    {
        $this->table = $table;
        $schema = $this->getItems($fields);
        return implode(PHP_EOL."\t\t\t", $schema);
    }

    /**
     * Return string for adding all foreign keys
     *
     * @param array $items
     * @return array
     */
    public function getItems($items)
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = $this->getItem($item);
        }
        return $result;
    }

    /**
     * @param array $item
     * @return string
     */
    abstract protected function getItem($item);

    /**
     * @param $decorators
     * @return string
     */
    protected function addDecorators($decorators)
    {
        $output = '';
        foreach ($decorators as $decorator) {
            $output .= sprintf("->%s", $decorator);
            // Do we need to tack on the parens?
            if (strpos($decorator, '(') === false) {
                $output .= '()';
            }
        }
        return $output;
    }
}
