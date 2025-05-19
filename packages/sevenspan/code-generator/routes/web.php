<?php

use Illuminate\Support\Facades\Route;
use Sevenspan\CodeGenerator\Http\Livewire\Index;
use Sevenspan\CodeGenerator\Http\Livewire\LogTable;

// Define the route for the code generator
// The route path is configurable via the 'route_path' option in the code_generator config file
Route::get(
    config("code_generator.route_path"),
    function () {
        return view('code-generator::livewire.index');
    }
)->middleware("codeGeneratorMiddleware")->name('code-generator.index');

Route::get(
    config("code_generator.logs_path"),
    function () {
        return view('code-generator::livewire.index');
    }
    //Logs::class
)->middleware("codeGeneratorMiddleware")->name('code-generator.logs');