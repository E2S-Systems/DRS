<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route:prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->middleware('auth:sanctum')->name('logout');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
    });
});