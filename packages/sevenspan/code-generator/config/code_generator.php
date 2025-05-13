<?php

use Sevenspan\CodeGenerator\Http\Middleware\AuthorizeCodeGenerator;

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | This option defines the middleware that will be applied to the routes
    | of the code generator. You can add or remove middleware as needed.
    |
    */
    'middleware' => [
        'web',
        AuthorizeCodeGenerator::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Path
    |--------------------------------------------------------------------------
    |
    | This option specifies the base URI path for the code generator routes.
    | You can customize this path to suit your application's requirements.
    |
    */
    'route_path' => 'code-generator',

    /*
    |--------------------------------------------------------------------------
    | Enable Code Generator in Production
    |--------------------------------------------------------------------------
    |
    | This option determines whether the code generator should require
    | authentication in the production environment. Set this to true to
    | enforce authentication in production.
    |
    */
    'require_auth_in_production' => false,

];
