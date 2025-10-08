<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',     [AuthController::class, 'me']);
    Route::post('/logout',[AuthController::class, 'logout']);

    // Contoh endpoint berdasarkan role:
    Route::get('/admin/dashboard', fn() => ['ok' => true])
        ->middleware('role:admin');

    Route::get('/staff/divisi/tasks', fn() => ['ok' => true])
        ->middleware('role:staff_divisi');

    Route::get('/user/profile', fn() => ['ok' => true])
        ->middleware('role:user|admin|staff_divisi'); // banyak role sekaligus

        Route::middleware('auth:sanctum')->group(function () {
            // admin only
            Route::middleware('role:admin')->group(function () {
                Route::get('/users', [UserController::class,'index']);
                Route::post('/users', [UserController::class,'store']);
                Route::get('/users/{user}', [UserController::class,'show']);
                Route::put('/users/{user}', [UserController::class,'update']);
                Route::delete('/users/{user}', [UserController::class,'destroy']);
                Route::patch('/users/{user}/assign-division', [UserController::class,'assignDivision']);
            });
        
            // admin OR owner
            Route::patch('/users/{user}/change-password', [UserController::class,'changePassword']);
        });
});
