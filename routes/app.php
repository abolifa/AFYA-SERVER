<?php

use App\Http\Controllers\Flutter\AuthController;
use App\Http\Controllers\Flutter\HomeController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/check-national-id', [AuthController::class, 'checkNationalId']);

Route::get('/home', [HomeController::class, 'index'])->middleware('auth:sanctum');
