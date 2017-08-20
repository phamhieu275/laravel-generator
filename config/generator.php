<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Path for classes
    |--------------------------------------------------------------------------
    |
    | All Classes will be created on these relevant path
    |
    */
    'path' => [
        'template' => base_path('resources/bake_templates'),

        'migration' => base_path('database/migrations'),

        'model' => app_path('Models'),

        'controller' => app_path('Http/Controllers'),

        'view' => base_path('resources/views'),

        'request' => app_path('Http/Requests'),

        'route' => app_path('routes.php'),

        'factory' => database_path('factories'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Namespace for classes
    |--------------------------------------------------------------------------
    |
    | All classes will be created with these namespaces
    |
    */
    'namespace' => [
        'model'          => 'App\\Models\\',

        'controller'     => 'App\\Http\\Controllers',
    ],

    /*
     |--------------------------------------------------------------------------
     | View extend
     |--------------------------------------------------------------------------
     */
     'view' => [
        'layout' => 'default'
     ],

    /*
     |--------------------------------------------------------------------------
     | Package
     |--------------------------------------------------------------------------
     */
    'package' => [
        'base_path'           => base_path(),
    ]
];
