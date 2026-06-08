<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankDetailController;
use App\Http\Controllers\Api\KycController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Motorzad Mobile API
|--------------------------------------------------------------------------
|
| All routes here are prefixed with /api automatically.
| Authentication: Laravel Sanctum Token (Bearer Token in Authorization header).
|
*/

// ─── Public Routes (No Auth) ───────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
});

Route::prefix('otp')->group(function () {
    Route::post('send', [\App\Http\Controllers\Api\OtpController::class, 'sendOtp']);
    Route::post('verify', [\App\Http\Controllers\Api\OtpController::class, 'verifyOtp']);
});

// ─── Authenticated Routes ──────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::put('change-password', [AuthController::class, 'changePassword']);
        Route::post('photo', [AuthController::class, 'uploadPhoto']);
        Route::post('email/verify', [AuthController::class, 'verifyEmail']);
        Route::post('email/resend', [AuthController::class, 'resendVerificationEmail']);
    });

    // KYC
    Route::prefix('kyc')->group(function () {
        Route::get('/', [KycController::class, 'show']);
        Route::post('/', [KycController::class, 'store']);
        Route::get('status', [\App\Http\Controllers\Api\KycApiController::class, 'status']);
        Route::post('submit', [\App\Http\Controllers\Api\KycApiController::class, 'submit']);
        Route::get('history', [\App\Http\Controllers\Api\KycApiController::class, 'history']);
    });


    // Wallet
    Route::prefix('wallet')->group(function () {
        Route::get('/', [WalletController::class, 'show']);
        Route::get('transactions', [WalletController::class, 'transactions']);
        Route::get('deposits', [WalletController::class, 'deposits']);
        Route::get('withdrawals', [WalletController::class, 'withdrawals']);
        Route::post('deposit', [WalletController::class, 'requestDeposit']);
        Route::post('withdraw', [WalletController::class, 'requestWithdrawal']);
    });

    // Platform Bank Accounts (for deposit)
    Route::get('bank-accounts', [WalletController::class, 'bankAccounts']);

    // User Bank Details
    Route::prefix('bank-details')->group(function () {
        Route::get('/', [BankDetailController::class, 'show']);
        Route::put('/', [BankDetailController::class, 'update']);
    });

    // Auctions
    Route::prefix('auctions')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AuctionController::class, 'index']);
        Route::get('/watchlist', [\App\Http\Controllers\Api\AuctionController::class, 'watchlist']);
        Route::get('/{auction}', [\App\Http\Controllers\Api\AuctionController::class, 'show']);
        Route::get('/{auction}/bids', [\App\Http\Controllers\Api\AuctionController::class, 'bids']);
        Route::post('/{auction}/watch', [\App\Http\Controllers\Api\AuctionController::class, 'toggleWatch']);
        Route::post('/{auction}/bid', [\App\Http\Controllers\Api\BidController::class, 'store']);
    });

    // My Bids & Won Auctions
    Route::prefix('my')->group(function () {
        Route::get('/bids', [\App\Http\Controllers\Api\BidController::class, 'myBids']);
        Route::get('/won', [\App\Http\Controllers\Api\BidController::class, 'wonAuctions']);
    });
});

// ─── API Documentation Redirect ─────────────────────────────────────────────
Route::get('documentations', function () {
    return redirect()->route('l5-swagger.default.api');
});

// ─── Fallback ──────────────────────────────────────────────────────────────
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Endpoint not found.',
    ], 404);
});
