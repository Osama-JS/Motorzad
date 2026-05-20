<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OtpController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/otp/send', [OtpController::class, 'sendOtp']);
Route::post('/otp/verify', [OtpController::class, 'verifyOtp']);

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/check-token', [AuthController::class, 'checkToken']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify.api');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/email/resend', [AuthController::class, 'resendEmailVerification']);
    Route::post('/user/profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    Route::delete('/user/delete', [AuthController::class, 'deleteAccount']);
});
