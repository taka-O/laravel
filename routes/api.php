<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Middleware\AdminPrivilege;

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

Route::middleware([JwtMiddleware::class, AdminPrivilege::class])->group(function () {
    Route::group([
        'namespace' => 'App\Http\Controllers\admin',
        'prefix' => 'admin',
    ], function () {
        Route::get('users', 'UserController@index');
        Route::post('users', 'UserController@store');
        Route::put('users/{id}', 'UserController@update');
    });
});
