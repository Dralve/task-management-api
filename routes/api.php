<?php

use App\Http\Controllers\Api\Auth\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('/auth/v1')->group(function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('/current/user', [AuthController::class, 'current'])->middleware('auth:api');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');;
});

Route::prefix('/v1')->group(function(){
    Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::apiResource('/tasks', TaskController::class);
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore']);
});

Route::prefix('/v1')->group(function(){
    Route::apiResource('/users', UserController::class);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
});
