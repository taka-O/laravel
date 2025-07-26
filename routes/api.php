<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminPrivilege;

Route::get('/', function () {
    return 'OK';
});

Route::group([
    'middleware' => 'api',
], function () {
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/send_reset_password', [AuthController::class, 'sendResetPassword']);
        Route::post('/reset_password', [AuthController::class, 'resetPassword']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/current', [AuthController::class, 'me']);
    });

    Route::group([
        'namespace' => 'App\Http\Controllers',
    ], function () {
        Route::get('/announcements', 'AnnouncementController@index');
        Route::get('/courses', 'CourseController@index');
    });

    Route::middleware([
        AdminPrivilege::class,
    ])->group(function () {
        Route::group([
            'namespace' => 'App\Http\Controllers\Admin',
            'prefix' => 'admin',
        ], function () {
            Route::get('users', 'UserController@index');
            Route::post('users', 'UserController@store');
            Route::put('users/{id}', 'UserController@update');
        });
    });
});
