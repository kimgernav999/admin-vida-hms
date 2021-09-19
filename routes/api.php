<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\TokenController;

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
    Route::post('create', [AccountsController::class, 'create']);
    Route::post('signin', [AccountsController::class, 'signin']);
    Route::post('signout', [AccountsController::class, 'signout']);
    Route::post('current', [AccountsController::class, 'current']);
    Route::post('updateProfile', [AccountsController::class, 'updateProfile']);
    Route::post('updateUsername', [AccountsController::class, 'updateUsername']);
    Route::post('updatePassword', [AccountsController::class, 'updatePassword']);
    Route::post('delete', [AccountsController::class, 'delete']);
    Route::post('allAccounts', [AccountsController::class, 'allAccounts']);
    Route::get('test', [AccountsController::class, 'test']);
    Route::get('checkEmail', [AccountsController::class, 'checkEmail']);
    Route::get('checkUsername', [AccountsController::class, 'checkUsername']);
});

Route::prefix('token')->group(function () {
    Route::post('{type}/create', [TokenController::class, 'createToken']);
    Route::get('verifyToken', [TokenController::class, 'verifyToken']);
    Route::post('updatePassword', [TokenController::class, 'updatePassword']);
});
