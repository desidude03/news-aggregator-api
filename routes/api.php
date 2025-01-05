<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\UserController;

Route::post('/auth/token', [AuthController::class, 'generateToken']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/articles', [ArticlesController::class, 'index']);
    Route::get('/articles/{id}', [ArticlesController::class, 'show']); 

    Route::post('/users/preferences', [UserController::class, 'setPreferences']);
    Route::get('/users/preferences', [UserController::class, 'getPreferences']);
    Route::get('/users/feed', [UserController::class, 'getPersonalizedFeed']);
});
