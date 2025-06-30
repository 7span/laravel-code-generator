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
        'default' => [
            'model' => 'app/Models',
            'migration' => 'database/migrations',
            'factory' => 'database/factories',
            'notification' => 'app/Notifications',
            'observer' => 'app/Observers',
            'policy' => 'app/Policies',
            'service' => 'app/Services',
            'controller' => 'app/Http/Controllers/Api',
            'request' => 'app/Http/Requests',
            'resource' => 'app/Http/Resources',
            'trait' => 'app/Traits',
        ],
        'custom' => [
            'admin_controller' => 'app/Http/Controllers/Api/Admin',
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    |  Delete logs older than configured days
    |--------------------------------------------------------------------------
    */

    'log_retention_days' => env('CODE_GENERATOR_LOG_RETENTION_DAYS', 2),
];
