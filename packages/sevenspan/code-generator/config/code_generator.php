<?php

use Sevenspan\CodeGenerator\Http\Middleware\AuthorizeCodeGenerator;

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
    | These paths specify where generated files will be saved within the `app` directory,
    | and they also determine the corresponding namespaces for those files.
    | For example, if model_path is set to 'Models', generated models will be placed in
    | app/Models and will have the namespace App\Models.
    |
    */

    // Path for model files (default: app/Models)
    "model_path" => "Models",

    // Path for migration files (default: app/Migrations)
    "migration_path" => "Migrations",

    // Path for factory files (default: app/Factories)
    "factory_path" => "Factories",

    // Path for notification files (default: app/Notifications)
    "notification_path" => "Notifications",

    // Path for observer files (default: app/Observers)
    "observer_path" => "Observers",

    // Path for policy files (default: app/Policies)
    "policy_path" => "Policies",

    // Path for service files (default: app/Services)
    "service_path" => "Services",

    // Path for controller files (default: app/Http/Controllers)
    "controller_path" => "Http\Controllers",

    // Path for request files (default: app/Http/Requests)
    "request_path" => "Http\Requests",

    // Path for resource files (default: app/Http/Resources)
    "resource_path" => "Http\Resources",

    // Path for trait files (default: app/Traits)
    "trait_path" => "Traits",

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
    | Middleware for Code Generator Routes
    |--------------------------------------------------------------------------
    |
    | Define the middleware that will be applied to all code generator routes.
    | You can add more middleware or remove existing ones as per your app's
    | requirements.
    |
    */

    "middleware" => [
        "web", // Required for session and CSRF handling
        AuthorizeCodeGenerator::class, // Custom middleware to authorize generator access
    ],
];
