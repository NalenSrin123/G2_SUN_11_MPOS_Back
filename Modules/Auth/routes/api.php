<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;

Route::prefix('v1/auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('send-otp', [AuthController::class, 'sendOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('forgot-password', [AuthController::class, 'sendPasswordReset']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('auths', AuthController::class)->names('auth');
});
