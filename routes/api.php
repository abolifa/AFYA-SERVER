<?php

use App\Http\Controllers\Api\Patient\AuthController;
use App\Http\Controllers\Api\Patient\CenterController;
use App\Http\Controllers\Api\Patient\HomeController;
use App\Http\Controllers\Api\Patient\OrderController;
use App\Http\Controllers\Api\Patient\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Patient routes
Route::prefix('patient')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/home', [HomeController::class, 'index'])->middleware('auth:sanctum');
    Route::put('/update', [AuthController::class, 'update'])->middleware('auth:sanctum');
    Route::post('/upload-image', [AuthController::class, 'uploadImage'])->middleware('auth:sanctum');

});

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('{order}', [OrderController::class, 'show']);
    Route::put('{order}', [OrderController::class, 'update']);
    Route::patch('{order}/cancel', [OrderController::class, 'cancel']);
    Route::delete('{order}', [OrderController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
});

Route::middleware('auth:sanctum')->prefix('centers')->group(function () {
    Route::get('/', [CenterController::class, 'index']);
});


