<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;

//posts
// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('/posts', PostController::class);
//     Route::put('/posts', [PostController::class, 'update']);
// });

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/user-profile', [AuthController::class, 'userProfile'])->name('userProfile');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('/posts', PostController::class);
    Route::put('/posts', [PostController::class, 'update']);
});
