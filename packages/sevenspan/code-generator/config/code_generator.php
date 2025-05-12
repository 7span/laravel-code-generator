<?php

use Sevenspan\CodeGenerator\Http\Middleware\AuthorizeCodeGenerator;

return [

    'middleware' => [
        'web',
        AuthorizeCodeGenerator::class

    ],
    'route_path' => 'laravel-code-generator',
    'require_code_gen_in_production' => false,

];
