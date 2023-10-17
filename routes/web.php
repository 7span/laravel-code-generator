<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakeFileController;
use App\Http\Controllers\MakeTypeController;
use App\Http\Controllers\MakeQueryController;
use App\Http\Controllers\MakeMutationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('make-files');
});

Route::post('make-files', [MakeFileController::class, 'store']);

Route::post('type-fields-and-datatypes', [MakeTypeController::class, 'fieldsAndDatatypes']);
Route::post('make-type', [MakeTypeController::class, 'store']);

Route::post('query-fields-and-datatypes', [MakeTypeController::class, 'fieldsAndDatatypes']);
Route::post('make-query', [MakeQueryController::class, 'store']);

Route::post('mutation-fields-and-datatypes', [MakeTypeController::class, 'fieldsAndDatatypes']);
Route::post('make-mutation', [MakeMutationController::class, 'store']);
