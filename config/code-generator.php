<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Path
    |--------------------------------------------------------------------------
    |
    | Defines the URI prefix for accessing code generator.
    | Example: If set to 'code-generator', routes will be accessible at
    | yourdomain.com/code-generator/...
    |
    */
    "route_path" => "code-generator",

    /*
    |--------------------------------------------------------------------------
    | Paths for Generated Files
    |--------------------------------------------------------------------------
    |
    | These paths specify where generated files will be saved 
    | and they also determine the corresponding namespaces for those files.
    | For example, if the model path is 'App\Models\Abc', models will be generated in app/Models/Abc
    | with the namespace App\Models\Abc.
    |
    */

    'paths' => [
        'model' => 'App\Models',
        'migration' => 'Database\Migrations',
        'factory' => 'Database\Factories',
        'notification' => 'App\Notifications',
        'observer' => 'App\Observers',
        'policy' => 'App\Policies',
        'service' => 'App\Services',
        'controller' => 'App\Http\Controllers',
        'admin_controller' => 'App\Http\Controllers\Admin',
        'request' => 'App\Http\Requests',
        'resource' => 'App\Http\Resources',
        'trait' => 'App\Traits',
    ],

    /*
    |--------------------------------------------------------------------------
    |  Delete logs older than configured days
    |--------------------------------------------------------------------------
    */

    'log_retention_days' => env('CODE_GENERATOR_LOG_RETENTION_DAYS', 2),
];
