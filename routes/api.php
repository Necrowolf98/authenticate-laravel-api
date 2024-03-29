<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(App\Http\Controllers\UserController::class)
    ->prefix('users')
    ->name('users.')
    ->group(function() {
        Route::get('/', 'userLogged')->name('logged');
        Route::get('/list', 'index')->name('list');

    });
});
