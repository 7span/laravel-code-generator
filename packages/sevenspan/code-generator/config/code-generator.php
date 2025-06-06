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
    | These paths specify where generated files will be saved within the app directory,
    | and they also determine the corresponding namespaces for those files.
    | For example, if the model path is 'Models\Abc', models will be generated in app/Models/Abc
    | with the namespace App\Models\Abc.
    |
    */

    'paths' => [
        'model' => 'Models',
        'migration' => 'Migrations',
        'factory' => 'Factories',
        'notification' => 'Notifications',
        'observer' => 'Observers',
        'policy' => 'Policies',
        'service' => 'Services',
        'controller' => 'Http\Controllers',
        'admin_controller' => 'Http\Controllers\Admin',
        'request' => 'Http\Requests',
        'resource' => 'Http\Resources',
        'trait' => 'Traits',
    ],

    /*
    |--------------------------------------------------------------------------
    | Require Authentication in Production
    |--------------------------------------------------------------------------
    |
    | Set to true if you want to restrict access to the code generator
    | in production using authentication middleware.
    | This is recommended for security reasons in production environments.
    |
    */

    "require_auth_in_production" => false,

    /*
    |--------------------------------------------------------------------------
    |  Delete logs older than configured days
    |--------------------------------------------------------------------------
    */

    'log_retention_days' => env('CODE_GENERATOR_LOG_RETENTION_DAYS', 2),
];
