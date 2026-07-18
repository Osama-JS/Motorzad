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

// General App Data
Route::prefix('general')->group(function () {
    Route::get('settings', [\App\Http\Controllers\Api\GeneralController::class, 'settings']);
    Route::get('faqs', [\App\Http\Controllers\Api\GeneralController::class, 'faqs']);
    Route::get('vehicle-options', [\App\Http\Controllers\Api\GeneralController::class, 'vehicleOptions']);
    Route::get('featured-auctions', [\App\Http\Controllers\Api\GeneralController::class, 'featuredAuctions']);
    Route::get('auctions', [\App\Http\Controllers\Api\GeneralController::class, 'auctions']);
    Route::get('search', [\App\Http\Controllers\Api\GeneralController::class, 'search']);
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
        
        // Auto Bid Settings
        Route::post('auto-bid-settings', [AuthController::class, 'updateAutoBidSettings']);
    });

    // KYC
    Route::prefix('kyc')->group(function () {
        Route::get('/status', [\App\Http\Controllers\Api\KycApiController::class, 'status']);
        Route::get('/history', [\App\Http\Controllers\Api\KycApiController::class, 'history']);
        Route::post('/submit', [\App\Http\Controllers\Api\KycApiController::class, 'submit']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
        Route::post('/fcm-token', [\App\Http\Controllers\Api\NotificationController::class, 'updateFcmToken']);
        Route::post('/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
        Route::post('/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
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

    // Vehicles
    Route::prefix('vehicles')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\VehicleController::class, 'store']);
        Route::get('/my', [\App\Http\Controllers\Api\VehicleController::class, 'index']);
        Route::get('/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'show']);
        Route::put('/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'update']);
        Route::delete('/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'destroy']);
        Route::post('/{vehicle}/images', [\App\Http\Controllers\Api\VehicleController::class, 'uploadImages']);
        Route::delete('/{vehicle}/images/{image}', [\App\Http\Controllers\Api\VehicleController::class, 'deleteImage']);
    });

    // Auctions
    Route::prefix('auctions')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AuctionController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\AuctionController::class, 'store']);
        Route::get('/my', [\App\Http\Controllers\Api\AuctionController::class, 'myAuctions']);
        Route::get('/watchlist', [\App\Http\Controllers\Api\AuctionController::class, 'watchlist']);
        Route::get('/{auction}', [\App\Http\Controllers\Api\AuctionController::class, 'show']);
        Route::put('/{auction}', [\App\Http\Controllers\Api\AuctionController::class, 'update']);
        Route::delete('/{auction}', [\App\Http\Controllers\Api\AuctionController::class, 'destroy']);
        Route::post('/{auction}/images', [\App\Http\Controllers\Api\AuctionController::class, 'uploadImages']);
        Route::delete('/{auction}/images/{image}', [\App\Http\Controllers\Api\AuctionController::class, 'deleteImage']);
        
        Route::get('/{auction}/bids', [\App\Http\Controllers\Api\AuctionController::class, 'bids']);
        Route::post('/{auction}/watch', [\App\Http\Controllers\Api\AuctionController::class, 'toggleWatch']);
        Route::post('/{auction}/bid', [\App\Http\Controllers\Api\AuctionController::class, 'placeBid']);
        Route::put('/{auction}/bids/{bid}', [\App\Http\Controllers\Api\AuctionController::class, 'updateBid']);
    });

    // My Bids & Won Auctions
    Route::prefix('my')->group(function () {
        Route::get('/bids', [\App\Http\Controllers\Api\AuctionController::class, 'myBids']);
        Route::get('/won', [\App\Http\Controllers\Api\AuctionController::class, 'wonAuctions']);
    });

    // Orders (Post Auction Checkout)
    Route::prefix('orders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\OrderController::class, 'index']);
        Route::get('/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
        Route::put('/{order}/checkout', [\App\Http\Controllers\Api\OrderController::class, 'checkout']);
    });

    // Support Tickets
    Route::prefix('support')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\SupportController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\SupportController::class, 'store']);
        Route::get('/{ticket}', [\App\Http\Controllers\Api\SupportController::class, 'show']);
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Api\SupportController::class, 'reply']);
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
