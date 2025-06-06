<?php

use Illuminate\Support\Facades\Route;

// Define the route for the code generator
// The route path is configurable via the 'route_path' option in the code-generator config file
Route::get(
    config("code-generator.route_path"),
    function () {
        return view('code-generator::livewire.index');
    }
)->name('code-generator.index');

// Define the route for logs
Route::get(
    'code-generator/logs',
    function () {
        return view('code-generator::livewire.index');
    }
)->name('code-generator.logs');
