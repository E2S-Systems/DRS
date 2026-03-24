<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::controller(\App\Http\Controllers\AuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('logout', 'logout')->middleware('auth:sanctum')->name('logout');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\UserController::class);
        Route::apiResource('branches', \App\Http\Controllers\BranchController::class);
    });
});
