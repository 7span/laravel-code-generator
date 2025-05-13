<?php

use Illuminate\Support\Facades\Route;

// Group routes under the 'codeGeneratorMiddleware' middleware
Route::middleware('codeGeneratorMiddleware')->group(function () {

    // Define a route for the code generator
    Route::get(config('code-generator.route_path'), function () {
        // TODO: Replace this with the appropriate view to be returned by the frontend
        return "in laravel code generator";
    });
});
