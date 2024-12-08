<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FollowingController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::controller(PostController::class)->group(function () {
            Route::post('/posts', 'store');
            Route::delete('/posts/{id}', 'destroy');
            Route::get('/posts', 'index');
        });
        Route::controller(FollowingController::class)->group(function () {
            Route::post('/users/{user:username}/follow', 'follow');
            Route::delete('/users/{user:username}/unfollow', 'unfollow');
            Route::get('/users/{user:username}/following', 'getFollowing');
            Route::get('/users/{user:username}/followers', 'getFollowers');
            Route::put('/users/{user:username}/accept', 'accept');
        });
        Route::controller(UserController::class)->group(function (){
            Route::get('/users', 'index');
            Route::get('/users/{user:username}', 'detail');
        });
    });
});
