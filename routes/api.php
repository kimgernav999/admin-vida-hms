<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('accounts')->group(function () {
    Route::post('signup', [AccountsController::class, 'signup']);
    Route::post('signin', [AccountsController::class, 'signin']);
    Route::post('signout', [AccountsController::class, 'signout']);
    Route::post('current', [AccountsController::class, 'current']);
    Route::post('updateProfile', [AccountsController::class, 'updateProfile']);
    Route::post('updateUsername', [AccountsController::class, 'updateUsername']);
    Route::post('updatePassword', [AccountsController::class, 'updatePassword']);
    Route::get('test', [AccountsController::class, 'test']);
    Route::get('checkEmail', [AccountsController::class, 'checkEmail']);
    Route::get('checkUsername', [AccountsController::class, 'checkUsername']);
});
