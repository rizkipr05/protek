<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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
});
