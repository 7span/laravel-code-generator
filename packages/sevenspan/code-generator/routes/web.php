<?php

use Illuminate\Support\Facades\Route;

Route::middleware('codeGeneratorMiddleware')->group(function () {

    Route::get(config('code-generator.route_path'),  function () {
        return "in laravel code generator";  //TODO : view to be return by frontend 
    });
});
