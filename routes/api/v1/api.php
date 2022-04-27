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

Route::middleware('guest')->group(function () {
    Route::post('login', 'Api\v1\AuthController@login')->name('login');
    Route::post('refresh-token', 'Api\v1\AuthController@refreshToken')->name('refreshToken');
});

Route::middleware('auth:api')->group(function () {
    Route::group(['middleware' => ['auth:api']], function (){
        Route::post('logout', 'Api\v1\AuthController@logout')->name('logout');
        Route::get('get-platform','Api\v1\RiotController@getPlatform');
        Route::get('get-champion','Api\v1\RiotController@getChampion');
    });
});
