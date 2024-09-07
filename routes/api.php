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

Route::prefix('v1')->group(function () {
    Route::get('titles', 'App\Http\Controllers\MovieController@getTitles');
});

Route::group(
    [
    'middleware' => 'api',
    'prefix' => 'auth' ],
    function () {
        Route::post('login', 'App\Http\Controllers\AuthController@login')->name('login');
    }
);

Route::get('need_login', function () {
    return response()->json('Unauthorized', 401);
})->name('need_login');
