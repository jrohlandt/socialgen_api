<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
    Registration & Login
*/
Route::post('/register', [AuthController::class, 'register'])->middleware(['throttle:user-registration']);
Route::post('/login', [AuthController::class, 'login'])->middleware(['throttle:user-login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [UserController::class, 'show'])->name('user.show');

    /*
        Dashboard
    */
    Route::get('/dashboard', [DashboardController::class, 'show'])->name('dashboard.show');

    /*
        Post Management
    */
    Route::resource('posts', PostController::class)->only('index', 'store', 'show', 'update', 'destroy');

    /*
        Content Generation
    */
    Route::post('/posts/generate', [PostController::class, 'generate'])->middleware(['throttle:post-generation']);
});
