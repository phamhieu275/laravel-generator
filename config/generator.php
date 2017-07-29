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

    'path_public_template'     => base_path('resources/generator_templates'),

    'path_migration'           => base_path('database/migrations/'),

    'path_model'               => app_path('Models/'),

    'path_repository'          => app_path('Repositories/'),

    'path_service'             => app_path('Services/'),

    'path_controller'          => app_path('Http/Controllers/'),

    'path_view'                => base_path('resources/views/'),

    'path_request'             => app_path('Http/Requests/'),

    'path_route'               => app_path('routes.php'),

    'path_factory'             => base_path('database/factories/'),

    /*
    |--------------------------------------------------------------------------
    | Namespace for classes
    |--------------------------------------------------------------------------
    |
    | All classes will be created with these namespaces
    |
    */

    'namespace_model'          => 'App\Models',

    'namespace_repository'     => 'App\Repositories',

    'namespace_service'        => 'App\Services',

    'namespace_controller'     => 'App\Http\Controllers',

    'namespace_request'        => 'App\Http\Requests',

    /*
     |--------------------------------------------------------------------------
     | View extend
     |--------------------------------------------------------------------------
     */
    'main_layout'           => 'layouts.default',

    /*
    |--------------------------------------------------------------------------
    | Scaffold setting
    |--------------------------------------------------------------------------
    |
    | Application layers consist of :
    |
    | Controllers - contains application logic and passing user input data to service
    | Services - The middleware between controller and repository,
    | gather data from controller, performs validation and business logic, calling repositories for data manipulation.
    | Repositories - layer for interaction with models and performing DB operations
    | Eloquents - common laravel model files with relationships defined
    |
    | By default scaffold will automatically service and repository layer.
    | You also can setting to only create repository
    | Or if you want to only use Eloquent, you can set 2 below options is false.
    */
    'use_repository_layer'  => false,

    'use_service_layer'     => false,

    /*
     |--------------------------------------------------------------------------
     | Package
     |--------------------------------------------------------------------------
     */
    'package_base_path'           => base_path(),
];
