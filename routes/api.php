<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'OK';
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/current', [AuthController::class, 'me']);
});

Route::middleware('jwt')->group(function () {
    Route::group([
        'namespace' => 'App\Http\Controllers\admin',
        'prefix' => 'admin',
    ], function () {
        Route::get('users', 'UserController@index');
        Route::post('users', 'UserController@create');
    });
});
