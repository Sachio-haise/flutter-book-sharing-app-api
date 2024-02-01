<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new UserResource($request->user());
});

Route::middleware(['throttle:api'])->controller(UserController::class)->group(function () {
    Route::post('login', 'login')->name('user-login');
    Route::post('register', 'register')->name('user-register');
    Route::post('logout', 'logout')->name('user-logout');
    Route::post('forget-password', 'forgetPassword')->name('user-forget-password');
    Route::post('reset-password', 'resetPassword')->name('user-reset-password');
});

Route::middleware(['auth:sanctum', 'api'])->controller(UserController::class)->group(function () {
    Route::post('update-profile', 'updateProfile')->name('user-profile-update');
    Route::post('change-password', 'changePassword')->name('user-password-change');
    Route::post('upload-profile', 'uploadProfile')->name('user-profile-upload');
});

Route::controller(BookController::class)->group(function () {
    Route::get('books', 'getBooks')->name('get-books');
});

Route::middleware(['auth:sanctum', 'api'])->controller(BookController::class)->group(function () {
    Route::post('cart', 'getCart')->name('get-cart');
    Route::post('add-book', 'addBook')->name('add-book');
    Route::post('update-book/{id}', 'updateBook')->name('update-book');
    Route::delete('delete-book/{id}', 'deleteBook')->name('delete-book');
    Route::post('react-book', 'reactBook')->name('react-book');
    Route::post('add-to-cart', 'addToCart')->name('add-to-cart');
    Route::post('book-info','bookInfo')->name('book-info');
});
