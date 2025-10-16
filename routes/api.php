<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\api\DivisionController;

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

            Route::middleware(['auth:sanctum'])->group(function () {
                // Hanya admin yang boleh create/update/delete
                Route::get('/divisions', [DivisionController::class, 'index']);
                Route::get('/divisions/{division}', [DivisionController::class, 'show']);
            
            Route::middleware(['can:manage-divisions'])->group(function () {
                Route::post('/divisions', [DivisionController::class, 'store']);
                Route::patch('/divisions/{division}', [DivisionController::class, 'update']);
                Route::delete('/divisions/{division}', [DivisionController::class, 'destroy']);
            
                Route::middleware(['role:admin'])->group(function () {
                    // ...
            });
        });
    });
  });
});