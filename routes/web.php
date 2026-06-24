<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('bidder')) {
        return redirect()->route('bidder.dashboard');
    }
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // KYC Routes
    Route::get('/kyc', [\App\Http\Controllers\KycController::class, 'index'])->name('kyc.index');
    Route::post('/kyc', [\App\Http\Controllers\KycController::class, 'store'])->name('kyc.store');
});

// مسارات إدارة حركات المحفظة المخصصة بالمعيار الصناعي (ذات أولوية توجيه عليا)
Route::prefix('admin/wallets/{wallet}')->middleware(['auth', 'role:admin'])->group(function () {
    // جلب سجل الحركات للجدول
    Route::get('/transactions', [WalletTransactionController::class, 'index'])->name('transactions.index');
    // حفظ معاملة جديدة من النافذة المنبثقة
    Route::post('/transactions', [WalletTransactionController::class, 'store'])->name('transactions.store');
    // جلب بيانات معاملة محددة للتعديل
    Route::get('/transactions/{transaction}/edit', [WalletTransactionController::class, 'edit'])->name('transactions.edit');
    // تحديث المعاملة المالية عبر AJAX
    Route::post('/transactions/{transaction}/update', [WalletTransactionController::class, 'update'])->name('transactions.update');
});

// Admin Management Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('pages/data', [\App\Http\Controllers\Admin\PageController::class, 'getData'])->name('pages.data');
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    Route::get('roles/data', [\App\Http\Controllers\Admin\RoleController::class, 'getData'])->name('roles.data');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::get('permissions/data', [\App\Http\Controllers\Admin\PermissionController::class, 'getData'])->name('permissions.data');
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/update-status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.update-status');
    Route::post('users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');
    Route::post('users/{user}/verify-identity', [\App\Http\Controllers\Admin\UserController::class, 'verifyIdentity'])->name('users.verify-identity');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Settings Routes
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');


     // Bank Accounts Management
    Route::get('bank-accounts/data', [App\Http\Controllers\Admin\BankAccountController::class, 'getData'])->name('bank-accounts.data');
    Route::post('bank-accounts/{id}/toggle-active', [App\Http\Controllers\Admin\BankAccountController::class, 'toggleActive'])->name('bank-accounts.toggle-active');
    Route::resource('bank-accounts', App\Http\Controllers\Admin\BankAccountController::class);

    // FAQs Management
    Route::get('faqs/data', [\App\Http\Controllers\Admin\FaqController::class, 'getData'])->name('faqs.data');
    Route::post('faqs/{faq}/toggle-active', [\App\Http\Controllers\Admin\FaqController::class, 'toggleActive'])->name('faqs.toggle-active');
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);

    // Wallets Management
    Route::get('wallets/data', [\App\Http\Controllers\Admin\WalletController::class, 'getData'])->name('wallets.data');
    Route::get('wallets', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallets.index');
    // Withdrawals Management
    Route::prefix('wallets/withdrawals')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::get('/{withdrawal}/details', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
        Route::post('/{withdrawal}/process', [\App\Http\Controllers\Admin\WithdrawalController::class, 'process'])->name('withdrawals.process');
    });

    Route::get('wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallets.show');
    Route::post('wallets/{wallet}/transaction', [\App\Http\Controllers\Admin\WalletController::class, 'storeTransaction'])->name('wallets.transactions.store');
    Route::post('wallets/{wallet}/debt-ceiling', [\App\Http\Controllers\Admin\WalletController::class, 'updateDebtCeiling'])->name('wallets.debt-ceiling.update');

    // Deposit Requests Management (previously MISSING)
    Route::get('deposits/data', [\App\Http\Controllers\Admin\DepositController::class, 'index'])->name('deposits.data');
    Route::get('deposits', [\App\Http\Controllers\Admin\DepositController::class, 'index'])->name('deposits.index');
    Route::get('deposits/{deposit}', [\App\Http\Controllers\Admin\DepositController::class, 'show'])->name('deposits.show');
    Route::post('deposits/{deposit}/process', [\App\Http\Controllers\Admin\DepositController::class, 'process'])->name('deposits.process');

    // Notifications Management
    Route::get('notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
    Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
    Route::get('notifications/latest-unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getLatestUnread'])->name('notifications.latest_unread');

    // Auctions Management
    Route::get('auctions/data', [\App\Http\Controllers\Admin\AuctionController::class, 'getData'])->name('auctions.data');
    Route::get('auctions/analytics', [\App\Http\Controllers\Admin\AuctionController::class, 'analytics'])->name('auctions.analytics');
    Route::get('auctions/export-report', [\App\Http\Controllers\Admin\AuctionController::class, 'exportReport'])->name('auctions.export-report');
    Route::post('auctions/{auction}/pause', [\App\Http\Controllers\Admin\AuctionController::class, 'pause'])->name('auctions.pause');
    Route::post('auctions/{auction}/resume', [\App\Http\Controllers\Admin\AuctionController::class, 'resume'])->name('auctions.resume');
    Route::post('auctions/{auction}/extend', [\App\Http\Controllers\Admin\AuctionController::class, 'extend'])->name('auctions.extend');
    Route::post('auctions/{auction}/force-end', [\App\Http\Controllers\Admin\AuctionController::class, 'forceEnd'])->name('auctions.force-end');
    Route::resource('auctions', \App\Http\Controllers\Admin\AuctionController::class);

    // Bids Management
    Route::get('bids/data', [\App\Http\Controllers\Admin\BidController::class, 'getData'])->name('bids.data');
    Route::get('bids', [\App\Http\Controllers\Admin\BidController::class, 'index'])->name('bids.index');

    // Vehicles Management
    Route::post('vehicles/decode-vin', [\App\Http\Controllers\Admin\VehicleController::class, 'decodeVin'])->name('vehicles.decode-vin');
    Route::get('vehicles/data', [\App\Http\Controllers\Admin\VehicleController::class, 'getData'])->name('vehicles.data');
    Route::post('vehicles/{vehicle}/approve', [\App\Http\Controllers\Admin\VehicleController::class, 'approve'])->name('vehicles.approve');
    Route::post('vehicles/{vehicle}/reject', [\App\Http\Controllers\Admin\VehicleController::class, 'reject'])->name('vehicles.reject');
    Route::delete('vehicles/images/{image}', [\App\Http\Controllers\Admin\VehicleController::class, 'deleteImage'])->name('vehicles.delete-image');
    Route::post('vehicles/images/{image}/set-primary', [\App\Http\Controllers\Admin\VehicleController::class, 'setPrimaryImage'])->name('vehicles.set-primary-image');
    Route::post('vehicles/images/reorder', [\App\Http\Controllers\Admin\VehicleController::class, 'reorderImages'])->name('vehicles.images.reorder');
    Route::post('vehicles/images/{image}/update', [\App\Http\Controllers\Admin\VehicleController::class, 'updateImage'])->name('vehicles.images.update');
    Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class);
});
// Bidder Management Routes
Route::prefix('bidder')->name('bidder.')->middleware(['auth', 'role:bidder'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Bidder\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'index'])->name('bank-details.index');
    Route::post('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'update'])->name('bank-details.update');

    // Wallet Routes
    Route::get('/wallet', [\App\Http\Controllers\Bidder\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [\App\Http\Controllers\Bidder\WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::post('/wallet/withdraw', [\App\Http\Controllers\Bidder\WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    Route::post('/wallet/deposit', [\App\Http\Controllers\Bidder\WalletController::class, 'requestDeposit'])->name('wallet.deposit');

    // Auctions Routes
    Route::get('/my-bids', [\App\Http\Controllers\Bidder\AuctionController::class, 'myBids'])->name('auctions.my-bids');
    Route::get('/auctions', [\App\Http\Controllers\Bidder\AuctionController::class, 'index'])->name('auctions.index');
    Route::get('/auctions/{id}', [\App\Http\Controllers\Bidder\AuctionController::class, 'show'])->name('auctions.show');
    Route::post('/auctions/{id}/bid', [\App\Http\Controllers\Bidder\AuctionController::class, 'placeBid'])->name('auctions.bid');
    Route::post('/auctions/{id}/watch', [\App\Http\Controllers\Bidder\AuctionController::class, 'toggleWatchlist'])->name('auctions.watch');
});

Route::get('/resources', function () {
    return view('resources');
})->name('resources');

Route::get('/page/{slug}', function ($slug) {
    $page = \App\Models\Page::where('slug', $slug)->firstOrFail();
    if (!$page->is_active && (!auth()->check() || !auth()->user()->hasRole('admin'))) {
        abort(404);
    }
    return view('page', compact('page'));
})->name('page.show');

require __DIR__.'/auth.php';
