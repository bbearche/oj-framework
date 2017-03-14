<?php

use Illuminate\Http\Request;

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



Route::group(['namespace' => 'Auth'], function () {
    Route::post('register', 'RegisterController@create');

    Route::post('login', 'LoginController');
});


Route::group(['middleware' => 'auth:api'], function () {
    Route::get('user', 'UserController@show');
    Route::get('user/{user_id}', 'UserController@show');
    Route::put('user', 'UserController@update');
    Route::post('user/profile-image', 'UserController@postProfileImage');
});
