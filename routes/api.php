<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\AccountsController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\AmenitiesController;
use App\Http\Controllers\AmenitiesCategoryController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\BookingController;

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

Route::prefix('amenities_category')->group(function () {
    Route::post('create', [AmenitiesCategoryController::class, 'create']);
    Route::post('update', [AmenitiesCategoryController::class, 'update']);
    Route::post('delete', [AmenitiesCategoryController::class, 'delete']);
    Route::get('allAmenitiesCategories', [AmenitiesCategoryController::class, 'allAmenitiesCategories']);
});

Route::prefix('amenities')->group(function () {
    Route::post('create', [AmenitiesController::class, 'create']);
    Route::post('update', [AmenitiesController::class, 'update']);
    Route::post('delete', [AmenitiesController::class, 'delete']);
    Route::get('allAmenities', [AmenitiesController::class, 'allAmenities']);
});

Route::prefix('rooms')->group(function () {
    Route::post('create', [RoomController::class, 'create']);
    Route::post('update', [RoomController::class, 'update']);
    Route::post('delete', [RoomController::class, 'delete']);
    Route::get('allRooms', [RoomController::class, 'allRooms']);
});

Route::prefix('roomtypes')->group(function () {
    Route::post('create', [RoomTypeController::class, 'create']);
    Route::post('update', [RoomTypeController::class, 'update']);
    Route::post('delete', [RoomTypeController::class, 'delete']);
    Route::get('allRoomTypes', [RoomTypeController::class, 'allRoomTypes']);
});

Route::prefix('offers')->group(function () {
    Route::post('create', [OffersController::class, 'create']);
    Route::post('update', [OffersController::class, 'update']);
    Route::post('delete', [OffersController::class, 'delete']);
    Route::get('allOffers', [OffersController::class, 'allOffers']);
});

Route::prefix('attachments')->group(function () {
    Route::post('create', [AttachmentController::class, 'create']);
    Route::get('delete', [AttachmentController::class, 'delete']);
    Route::get('viewDetails', [AttachmentController::class, 'viewDetails']);
});

Route::prefix('booking')->group(function () {
    Route::post('new', [BookingController::class, 'new']);
    Route::post('payBooking', [BookingController::class, 'payBooking']);
    Route::post('cancelBooking', [BookingController::class, 'cancelBooking']);
    Route::get('allBookings', [BookingController::class, 'allBookings']);
});
