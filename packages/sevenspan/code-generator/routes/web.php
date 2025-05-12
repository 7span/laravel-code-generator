<?php

use Illuminate\Support\Facades\Route;

Route::middleware('codeGenMiddleware')->group(function () {

    Route::get(config('laravel-code-generator.route_path'),  function () {
        return "in laravel code generator";
    });
});
