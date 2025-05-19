<?php

use Illuminate\Support\Facades\Route;

// Define the route for the code generator
// The route path is configurable via the 'route_path' option in the code_generator config file
Route::get(
    config("code_generator.route_path"),
    function () {
        // TODO: Replace this with the appropriate view to be returned by the frontend
        return "in laravel code generator";
    }
)->middleware("codeGeneratorMiddleware");
