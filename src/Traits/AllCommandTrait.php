<?php

namespace Bluecode\Generator\Traits;

trait AllCommandTrait
{
    /**
     * Get the list model base on only and exclude arguments
     *
     * @return array
     */
    public function getListModel()
    {
        if ($this->option('only')) {
            $models = explode(',', trim($this->option('only')));
        } else {
            $tables = $this->schema->getTables();
            $models = collect($tables)->map(function ($table) {
                return studly_case(str_singular($table));
            })->all();
        }

        if ($this->option('exclude')) {
            $excludeModels = explode(',', trim($this->option('exclude')));
            $models = array_diff($models, $excludeModels);
        }

        return $models;
    }
}
