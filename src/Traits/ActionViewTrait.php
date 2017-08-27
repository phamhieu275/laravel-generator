<?php

namespace Bluecode\Generator\Traits;

trait ActionViewTrait
{
    /**
     * Get the list view.
     *
     * @param string|null $actions The actions
     * @return string
     */
    public function getListView($actions)
    {
        $actionViews = [
            'index' => ['index', 'table'],
            'create' => ['create', 'form'],
            'edit' => ['edit', 'form'],
            'show' => ['show']
        ];

        $views = collect($actionViews);
        if (! empty($actions)) {
            $actions = explode(',', str_replace(' ', '', trim($actions)));
            $views = $views->only($actions);
        }

        return $views->flatten()->unique();
    }
}
