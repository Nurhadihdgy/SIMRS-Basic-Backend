<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PoliklinikController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\ScheduleController;

Route::post('/v3/auth/login', [AuthController::class, 'login']);
Route::post('/v3/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Poliklinik
    Route::post('/v3/poliklinik', [PoliklinikController::class, 'store']);
    Route::put('/v3/poliklinik/{id}', [PoliklinikController::class, 'update']);
    Route::delete('/v3/poliklinik/{id}', [PoliklinikController::class, 'destroy']);
    Route::get('/v3/poliklinik', [PoliklinikController::class, 'index']);

    // Doctor
    Route::post('/v3/doctor', [DoctorController::class, 'store']);
    Route::put('/v3/doctor/{id}', [DoctorController::class, 'update']);
    Route::delete('/v3/doctor/{id}', [DoctorController::class, 'destroy']);
    Route::get('/v3/doctor', [DoctorController::class, 'index']);

    // Schedule
    Route::post('/v3/schedule', [ScheduleController::class, 'store']);
    Route::put('/v3/schedule/{id}', [ScheduleController::class, 'update']);
    Route::delete('/v3/schedule/{id}', [ScheduleController::class, 'destroy']);
    Route::get('/v3/schedule', [ScheduleController::class, 'index']);
});
