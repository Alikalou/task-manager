<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('projects.tasks', TaskController::class);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::patch('/projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus']);
    });
});
