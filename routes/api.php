<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RobotController;
use Illuminate\Support\Facades\Route;

// ============================================== Auth ==============================================
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['verified', 'auth:sanctum']);
    Route::post('me', [AuthController::class, 'me'])->middleware(['verified', 'auth:sanctum']);
    // ============================================== Email verification ==============================================
    Route::post('email/verify-code', [AuthController::class, 'verifyEmail'])->middleware(['auth:sanctum']);
    Route::post('email/verify-code/resend', [AuthController::class, 'resendVerificationCode'])->middleware(['auth:sanctum']);
});

// ============================================== Robot/Launch ==============================================
Route::post('robot/launch', [RobotController::class, 'launch'])->middleware(['auth:sanctum']);
