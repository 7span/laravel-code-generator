<?php

use Illuminate\Support\Facades\Route;
use Sevenspan\CodeGenerator\Http\Livewire\Index;

//Route::get('/code-generator', Index::class)->name('code-generator.index');
Route::middleware('web')->group(function () {
    Route::get('/code-generator', function () {
        return view('code-generator::livewire.index');
    })->name('code-generator.index');
});