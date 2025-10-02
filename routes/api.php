<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\RobotController;
use Illuminate\Support\Facades\Route;

// ============================================== Auth ==============================================
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::post('me', [AuthController::class, 'me'])->middleware(['auth:sanctum']);

    Route::get('google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('google/callback', [GoogleAuthController::class, 'callback']);

    Route::post('email/verify-code', [EmailVerificationController::class, 'verifyEmail'])->middleware(['auth:sanctum']);
    Route::post('email/verify-code/resend', [EmailVerificationController::class, 'resendVerificationCode'])->middleware(['auth:sanctum']);
});

// ============================================== Robot/Launch ==============================================
Route::post('robot/launch', [RobotController::class, 'launch'])->middleware(['auth:sanctum']);
