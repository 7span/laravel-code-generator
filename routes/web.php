<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MakeFileController;
use App\Http\Controllers\MakeTypeController;

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
Route::post('make-type', [MakeTypeController::class, 'store']);
