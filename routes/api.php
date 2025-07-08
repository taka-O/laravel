<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'OK';
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('current', 'AuthController@me');
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'App\Http\Controllers\admin',
    'prefix' => 'admin'
], function () {
    Route::get('user', 'UserController@index');
    Route::post('user', 'UserController@create');
});