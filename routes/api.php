<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
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
    return $request->user();
});

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login')->name('user-login');
    Route::post('register', 'register')->name('user-register');
    Route::post('logout', 'logout')->name('user-logout');
})->middleware(['throttle:api']);


Route::controller(BookController::class)->group(function () {
    Route::get('books', 'getBooks')->name('get-books');
    Route::post('cart', 'getCart')->name('get-cart');
    Route::post('add-book', 'addBook')->name('add-book');
    Route::post('update-book/{id}', 'updateBook')->name('update-book');
    Route::delete('delete-book/{id}', 'deleteBook')->name('delete-book');
    Route::post('react-book', 'reactBook')->name('react-book');
    Route::post('add-to-cart', 'addToCart')->name('add-to-cart');
})->middleware('auth:sanctum');
