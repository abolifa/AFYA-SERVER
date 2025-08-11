<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\Api\Patient\AlertController;
use App\Http\Controllers\Api\Patient\AppointmentController;
use App\Http\Controllers\Api\Patient\AuthController;
use App\Http\Controllers\Api\Patient\CenterController;
use App\Http\Controllers\Api\Patient\HomeController;
use App\Http\Controllers\Api\Patient\OrderController;
use App\Http\Controllers\Api\Patient\PrescriptionController;
use App\Http\Controllers\Api\Patient\ProductController;
use App\Http\Controllers\AwarenessController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SiteStatisticController;
use App\Http\Controllers\SliderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::put('/update', [AuthController::class, 'update'])->middleware('auth:sanctum');
Route::post('/upload-image', [AuthController::class, 'uploadImage'])->middleware('auth:sanctum');
Route::post('/check-national-id', [AuthController::class, 'checkNationalId']);

Route::get('/home', [HomeController::class, 'index'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('{order}', [OrderController::class, 'show']);
    Route::put('{order}', [OrderController::class, 'update']);
    Route::patch('{order}/cancel', [OrderController::class, 'cancel']);
    Route::delete('{order}', [OrderController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('appointments')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::get('{id}', [AppointmentController::class, 'show']);
    Route::put('{id}', [AppointmentController::class, 'update']);
    Route::put('{id}/reschedule', [AppointmentController::class, 'reschedule']);
    Route::put('{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::delete('{id}', [AppointmentController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('prescriptions')->group(function () {
    Route::get('/', [PrescriptionController::class, 'index']);
    Route::get('/{id}', [PrescriptionController::class, 'show']);
});


Route::middleware('auth:sanctum')->prefix('appt-id')->group(function () {
    Route::get('/', [AppointmentController::class, 'getAppointmentsId']);
});

Route::middleware('auth:sanctum')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
});

Route::middleware('auth:sanctum')->prefix('centers')->group(function () {
    Route::get('/', [CenterController::class, 'index']);
    Route::get('/get', [CenterController::class, 'getCenters']);
    Route::get('{center}/doctors', [CenterController::class, 'getDoctors']);
    Route::get('{center}/schedule', [CenterController::class, 'getSchedule']);
});


Route::middleware('auth:sanctum')->prefix('alerts')->group(function () {
    Route::get('/', [AlertController::class, 'index']);
    Route::get('/{id}', [AlertController::class, 'show']);
    Route::post('/{id}/read', [AlertController::class, 'markAsRead']);
    Route::post('/read-all', [AlertController::class, 'markAllAsRead']);
    Route::delete('/{id}', [AlertController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/notifications', [AlertController::class, 'getNotifications']);


// site routes
Route::get('/sliders', [SliderController::class, 'index']);
Route::get('/centers/guest', [CenterController::class, 'index']);
Route::get('/numbers', [SiteStatisticController::class, 'index']);

Route::prefix('announcements')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index']);
    Route::get('{id}', [AnnouncementController::class, 'show']);
});


Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('{id}', [PostController::class, 'show']);
    Route::get('/{id}/related', [PostController::class, 'related']);
});


Route::prefix('awareness')->group(function () {
    Route::get('/', [AwarenessController::class, 'index']);
    Route::get('{id}', [AwarenessController::class, 'show']);
});

Route::post('/complaints', [ComplaintController::class, 'store']);



