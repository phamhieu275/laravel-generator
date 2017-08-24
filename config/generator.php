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
        'templates' => resource_path('vendor/laravel-generator1/templates'),

        'migration' => base_path('database/migrations'),

        'model' => app_path('Models'),

        'controller' => app_path('Http/Controllers'),

        'view' => resource_path('views'),
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
        'model'          => 'App\\Models',

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
