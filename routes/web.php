<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('/')->group(function () {
    // $route_groups = [];

    Route::get('', function (Request $request) {
        return view('index');
    });

    Route::get('{any}', function (Request $request) {
        return view('index');
    })->where('any', '.*');//'\b(?:(?!' . implode('|', $route_groups) . ')\b)\w+');
});
