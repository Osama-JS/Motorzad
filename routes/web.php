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
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
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
});
// Bidder Management Routes
Route::prefix('bidder')->name('bidder.')->middleware(['auth', 'role:bidder'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Bidder\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'index'])->name('bank-details.index');
    Route::post('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'update'])->name('bank-details.update');
});

Route::get('/resources', function () {
    return view('resources');
})->name('resources');

require __DIR__.'/auth.php';
