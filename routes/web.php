<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletTransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/debug-render', function() {
    $html = view('bidder.garage.create', ['vehicle' => null])->render();
    file_put_contents(public_path('debug.html'), $html);
    return 'Saved';
});

Route::get('/', function () {
    $featuredAuctions = \App\Models\Auction::with(['vehicle', 'highestBid'])
        ->live()
        ->featured()
        ->latest()
        ->take(3)
        ->get();

    $faqs = \App\Models\Faq::where('is_active', true)->latest()->get();
    
    $features = [];
    if (\Illuminate\Support\Facades\Schema::hasTable('features')) {
        $features = \App\Models\Feature::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    }
    
    // Get Hero Auction
    $heroAuctionId = \App\Models\Setting::get('hero_auction_id');
    if ($heroAuctionId) {
        $heroAuction = \App\Models\Auction::with(['vehicle', 'highestBid'])->find($heroAuctionId);
    } else {
        $heroAuction = \App\Models\Auction::with(['vehicle', 'highestBid'])
            ->live()
            ->featured()
            ->latest()
            ->first();
    }

    return view('welcome', compact('featuredAuctions', 'faqs', 'features', 'heroAuction'));
});

// Public Auctions
Route::get('/auctions', [\App\Http\Controllers\Frontend\AuctionController::class, 'index'])->name('frontend.auctions.index');
Route::get('/auctions/{id}', [\App\Http\Controllers\Frontend\AuctionController::class, 'show'])->name('frontend.auctions.show');

// Standalone Payment Gateway Routes (Web & Mobile WebView)
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/checkout/{transaction}', [\App\Http\Controllers\Web\PaymentController::class, 'checkout'])->name('checkout');
    Route::match(['get', 'post'], '/callback', [\App\Http\Controllers\Web\PaymentController::class, 'callback'])->name('callback');
    Route::get('/success', [\App\Http\Controllers\Web\PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [\App\Http\Controllers\Web\PaymentController::class, 'failure'])->name('failure');
});

// Mobile Developer Documentation
Route::get('/mobile-docs', function () {
    return view('docs.index');
})->name('docs.index');

Route::get('/contact', function () {
    return view('frontend.contact');
})->name('frontend.contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('frontend.contact.store');

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin') || $user->can('access-admin-panel')) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->hasRole('bidder') || $user->hasRole('seller')) {
        return redirect()->route('bidder.dashboard');
    }
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // KYC Routes (Require verified email)
    Route::middleware('verified')->group(function () {
        Route::get('/kyc', [\App\Http\Controllers\KycController::class, 'index'])->name('kyc.index');
        Route::post('/kyc', [\App\Http\Controllers\KycController::class, 'store'])->name('kyc.store');
    });

    // Account Deletion for Mobile Webview / Google Play Policy
    Route::get('/account-deletion', [\App\Http\Controllers\AccountDeletionController::class, 'show'])->name('account.delete.show');
    Route::post('/account-deletion', [\App\Http\Controllers\AccountDeletionController::class, 'destroy'])->name('account.delete.confirm');
});

// مسارات إدارة حركات المحفظة المخصصة بالمعيار الصناعي (ذات أولوية توجيه عليا)
Route::prefix('admin/wallets/{wallet}')->middleware(['auth', 'role_or_permission:admin|manage-wallets|view-wallets|edit-user-balance'])->group(function () {
    // جلب سجل الحركات للجدول
    Route::get('/transactions', [WalletTransactionController::class, 'index'])->middleware('permission:manage-wallets|view-wallets')->name('transactions.index');
    // حفظ معاملة جديدة من النافذة المنبثقة
    Route::post('/transactions', [WalletTransactionController::class, 'store'])->middleware('permission:manage-wallets|edit-user-balance')->name('transactions.store');
    // جلب بيانات معاملة محددة للتعديل
    Route::get('/transactions/{transaction}/edit', [WalletTransactionController::class, 'edit'])->middleware('permission:manage-wallets|edit-user-balance')->name('transactions.edit');
    // تحديث المعاملة المالية عبر AJAX
    Route::post('/transactions/{transaction}/update', [WalletTransactionController::class, 'update'])->middleware('permission:manage-wallets|edit-user-balance')->name('transactions.update');
});

// Admin Management Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role_or_permission:admin|access-admin-panel'])->group(function () {
    // ─── Dashboard & Global Search (Requires access-admin-panel) ─────────
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/global-search', [\App\Http\Controllers\Admin\DashboardController::class, 'globalSearch'])->name('global-search');

    // ─── System Health & Diagnostics ────────────────────────────────────
    Route::middleware('permission:manage-system-health')->group(function () {
        Route::get('system-status', [\App\Http\Controllers\Admin\SystemHealthController::class, 'index'])->name('system-status');
        Route::post('system-status/clear-cache', [\App\Http\Controllers\Admin\SystemHealthController::class, 'clearCache'])->name('system-status.clear-cache');
        Route::post('system-status/restart-queue', [\App\Http\Controllers\Admin\SystemHealthController::class, 'restartQueue'])->name('system-status.restart-queue');
        Route::post('system-status/optimize-clear', [\App\Http\Controllers\Admin\SystemHealthController::class, 'optimizeClear'])->name('system-status.optimize-clear');
        Route::post('system-status/toggle-maintenance', [\App\Http\Controllers\Admin\SystemHealthController::class, 'toggleMaintenance'])->name('system-status.toggle-maintenance');
        Route::get('system-status/json', \Spatie\Health\Http\Controllers\HealthCheckJsonResultsController::class)->name('system-status.json');
    });

    // ─── Roles & Permissions Management ─────────────────────────────────
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('roles/data', [\App\Http\Controllers\Admin\RoleController::class, 'getData'])->name('roles.data');
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::get('permissions/data', [\App\Http\Controllers\Admin\PermissionController::class, 'getData'])->name('permissions.data');
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    });

    // ─── Users Management (CRUD Granularity) ───────────────────────────
    Route::middleware('permission:manage-users|view-users')->group(function () {
        Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getData'])->name('users.data');
        Route::get('users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    });

    Route::middleware('permission:manage-users|edit-users')->group(function () {
        Route::get('users/create', [\App\Http\Controllers\Admin\UserController::class, 'create'])->name('users.create');
        Route::post('users', [\App\Http\Controllers\Admin\UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], 'users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
    });

    Route::middleware('permission:manage-users|verify-users-kyc')->group(function () {
        Route::post('users/{user}/update-status', [\App\Http\Controllers\Admin\UserController::class, 'updateStatus'])->name('users.update-status');
        Route::post('users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');
        Route::post('users/{user}/verify-identity', [\App\Http\Controllers\Admin\UserController::class, 'verifyIdentity'])->name('users.verify-identity');
    });

    Route::delete('users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])
        ->middleware('permission:manage-users|delete-users')
        ->name('users.destroy');

    // ─── Seller Requests & Dynamic Form Templates ───────────────────────
    Route::middleware('permission:manage-seller-requests')->group(function () {
        Route::get('seller-requests', [\App\Http\Controllers\Admin\SellerRequestController::class, 'index'])->name('seller-requests.index');
        Route::get('seller-requests/{sellerRequest}', [\App\Http\Controllers\Admin\SellerRequestController::class, 'show'])->name('seller-requests.show');
        Route::post('seller-requests/{sellerRequest}/approve', [\App\Http\Controllers\Admin\SellerRequestController::class, 'approve'])->name('seller-requests.approve');
        Route::post('seller-requests/{sellerRequest}/reject', [\App\Http\Controllers\Admin\SellerRequestController::class, 'reject'])->name('seller-requests.reject');
        Route::post('seller-requests/{sellerRequest}/modification', [\App\Http\Controllers\Admin\SellerRequestController::class, 'requestModification'])->name('seller-requests.modification');

        Route::patch('form-templates/{form_template}/toggle-status', [\App\Http\Controllers\Admin\FormTemplateController::class, 'toggleStatus'])->name('form-templates.toggle-status');
        Route::post('form-templates/{form_template}/clone', [\App\Http\Controllers\Admin\FormTemplateController::class, 'clone'])->name('form-templates.clone');
        Route::resource('form-templates', \App\Http\Controllers\Admin\FormTemplateController::class);
    });

    // ─── Settings Routes ────────────────────────────────────────────────
    Route::middleware('permission:manage-settings')->group(function () {
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [\App\Http\Controllers\Admin\SettingController::class, 'sendTestEmail'])->name('settings.test-email');
    });

    // ─── Backup Management ──────────────────────────────────────────────
    Route::middleware('permission:manage-backups')->group(function () {
        Route::get('backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [\App\Http\Controllers\Admin\BackupController::class, 'store'])->name('backups.store');
        Route::get('backups/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/bulk', [\App\Http\Controllers\Admin\BackupController::class, 'bulkDestroy'])->name('backups.bulk');
        Route::delete('backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('backups.destroy');
        Route::post('backups/settings', [\App\Http\Controllers\Admin\BackupController::class, 'updateSettings'])->name('backups.settings');
    });

    // ─── Contact Messages ───────────────────────────────────────────────
    Route::middleware('permission:manage-contacts')->group(function () {
        Route::get('contacts/data', [\App\Http\Controllers\Admin\ContactMessageController::class, 'getData'])->name('contacts.data');
        Route::get('contacts', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contacts.show');
        Route::delete('contacts/{contact}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contacts.destroy');
    });

    // ─── Bank Accounts Management ───────────────────────────────────────
    Route::middleware('permission:manage-bank-accounts')->group(function () {
        Route::get('bank-accounts/data', [App\Http\Controllers\Admin\BankAccountController::class, 'getData'])->name('bank-accounts.data');
        Route::post('bank-accounts/{id}/toggle-active', [App\Http\Controllers\Admin\BankAccountController::class, 'toggleActive'])->name('bank-accounts.toggle-active');
        Route::resource('bank-accounts', App\Http\Controllers\Admin\BankAccountController::class);
    });

    // ─── FAQs Management ────────────────────────────────────────────────
    Route::middleware('permission:manage-faqs')->group(function () {
        Route::get('faqs/data', [\App\Http\Controllers\Admin\FaqController::class, 'getData'])->name('faqs.data');
        Route::post('faqs/{faq}/toggle-active', [\App\Http\Controllers\Admin\FaqController::class, 'toggleActive'])->name('faqs.toggle-active');
        Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);
    });

    // ─── News Management ────────────────────────────────────────────────
    Route::middleware('permission:manage-news')->group(function () {
        Route::get('news/data', [\App\Http\Controllers\Admin\NewsController::class, 'getData'])->name('news.data');
        Route::post('news/{news}/toggle-active', [\App\Http\Controllers\Admin\NewsController::class, 'toggleActive'])->name('news.toggle-active');
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
    });

    // ─── Content Pages ──────────────────────────────────────────────────
    Route::middleware('permission:manage-pages')->group(function () {
        Route::get('pages/data', [\App\Http\Controllers\Admin\PageController::class, 'getData'])->name('pages.data');
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    });

    // ─── Marketing Features & Testimonials ──────────────────────────────
    Route::middleware('permission:manage-marketing')->group(function () {
        Route::get('features/data', [\App\Http\Controllers\Admin\FeatureController::class, 'getData'])->name('features.data');
        Route::post('features/{feature}/toggle-active', [\App\Http\Controllers\Admin\FeatureController::class, 'toggleActive'])->name('features.toggle-active');
        Route::resource('features', \App\Http\Controllers\Admin\FeatureController::class);

        Route::get('testimonials/data', [\App\Http\Controllers\Admin\TestimonialController::class, 'getData'])->name('testimonials.data');
        Route::post('testimonials/{testimonial}/toggle-active', [\App\Http\Controllers\Admin\TestimonialController::class, 'toggleActive'])->name('testimonials.toggle-active');
        Route::resource('testimonials', \App\Http\Controllers\Admin\TestimonialController::class);
    });

    // ─── Wallets Management (CRUD Granularity) ──────────────────────────
    Route::middleware('permission:manage-wallets|view-wallets')->group(function () {
        Route::get('wallets/data', [\App\Http\Controllers\Admin\WalletController::class, 'getData'])->name('wallets.data');
        Route::get('wallets', [\App\Http\Controllers\Admin\WalletController::class, 'index'])->name('wallets.index');
        Route::get('wallets/online-payments', [\App\Http\Controllers\Admin\WalletController::class, 'onlinePayments'])->name('wallets.online-payments');
        Route::get('wallets/{wallet}', [\App\Http\Controllers\Admin\WalletController::class, 'show'])->name('wallets.show');
    });
    Route::post('wallets/{wallet}/transaction', [\App\Http\Controllers\Admin\WalletController::class, 'storeTransaction'])
        ->middleware('permission:manage-wallets|edit-user-balance')
        ->name('wallets.transactions.store');
    Route::post('wallets/{wallet}/debt-ceiling', [\App\Http\Controllers\Admin\WalletController::class, 'updateDebtCeiling'])
        ->middleware('permission:manage-wallets|manage-debt-ceilings')
        ->name('wallets.debt-ceiling.update');

    // ─── Withdrawals Management ─────────────────────────────────────────
    Route::middleware('permission:manage-withdrawals')->group(function () {
        Route::prefix('wallets/withdrawals')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
            Route::get('/{withdrawal}/details', [\App\Http\Controllers\Admin\WithdrawalController::class, 'show'])->name('withdrawals.show');
            Route::post('/{withdrawal}/process', [\App\Http\Controllers\Admin\WithdrawalController::class, 'process'])->name('withdrawals.process');
        });
    });

    // ─── Deposit Requests Management ────────────────────────────────────
    Route::middleware('permission:manage-deposits')->group(function () {
        Route::get('deposits/data', [\App\Http\Controllers\Admin\DepositController::class, 'index'])->name('deposits.data');
        Route::get('deposits', [\App\Http\Controllers\Admin\DepositController::class, 'index'])->name('deposits.index');
        Route::get('deposits/{deposit}', [\App\Http\Controllers\Admin\DepositController::class, 'show'])->name('deposits.show');
        Route::post('deposits/{deposit}/process', [\App\Http\Controllers\Admin\DepositController::class, 'process'])->name('deposits.process');
    });

    // ─── Notifications Management ───────────────────────────────────────
    Route::middleware('permission:manage-notifications')->group(function () {
        Route::get('notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
        Route::post('notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.mark_read');
        Route::get('notifications/latest-unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getLatestUnread'])->name('notifications.latest_unread');
    });

    // ─── Auctions Management (CRUD Granularity) ─────────────────────────
    Route::middleware('permission:manage-all-auctions|view-admin-auctions')->group(function () {
        Route::get('auctions/data', [\App\Http\Controllers\Admin\AuctionController::class, 'getData'])->name('auctions.data');
        Route::get('auctions/analytics', [\App\Http\Controllers\Admin\AuctionController::class, 'analytics'])->name('auctions.analytics');
        Route::get('auctions/export-report', [\App\Http\Controllers\Admin\AuctionController::class, 'exportReport'])->name('auctions.export-report');
        Route::get('auctions', [\App\Http\Controllers\Admin\AuctionController::class, 'index'])->name('auctions.index');
        Route::get('auctions/{auction}', [\App\Http\Controllers\Admin\AuctionController::class, 'show'])->name('auctions.show');
    });

    Route::middleware('permission:manage-all-auctions|edit-auctions')->group(function () {
        Route::get('auctions/create', [\App\Http\Controllers\Admin\AuctionController::class, 'create'])->name('auctions.create');
        Route::post('auctions', [\App\Http\Controllers\Admin\AuctionController::class, 'store'])->name('auctions.store');
        Route::get('auctions/{auction}/edit', [\App\Http\Controllers\Admin\AuctionController::class, 'edit'])->name('auctions.edit');
        Route::match(['put', 'patch'], 'auctions/{auction}', [\App\Http\Controllers\Admin\AuctionController::class, 'update'])->name('auctions.update');
        Route::post('auctions/{auction}/extend', [\App\Http\Controllers\Admin\AuctionController::class, 'extend'])->name('auctions.extend');
    });

    Route::middleware('permission:manage-all-auctions|pause-auctions')->group(function () {
        Route::post('auctions/{auction}/pause', [\App\Http\Controllers\Admin\AuctionController::class, 'pause'])->name('auctions.pause');
        Route::post('auctions/{auction}/resume', [\App\Http\Controllers\Admin\AuctionController::class, 'resume'])->name('auctions.resume');
    });

    Route::post('auctions/{auction}/force-end', [\App\Http\Controllers\Admin\AuctionController::class, 'forceEnd'])
        ->middleware('permission:manage-all-auctions|force-end-auctions')
        ->name('auctions.force-end');

    Route::post('auctions/{auction}/block-user', [\App\Http\Controllers\Admin\AuctionController::class, 'blockUser'])
        ->middleware('permission:manage-all-auctions|block-auction-bidders')
        ->name('auctions.block-user');

    Route::delete('auctions/{auction}', [\App\Http\Controllers\Admin\AuctionController::class, 'destroy'])
        ->middleware('permission:manage-all-auctions|delete-auctions')
        ->name('auctions.destroy');

    // ─── Bids Management (CRUD Granularity) ─────────────────────────────
    Route::middleware('permission:manage-bids')->group(function () {
        Route::get('bids/data', [\App\Http\Controllers\Admin\BidController::class, 'getData'])->name('bids.data');
        Route::get('bids', [\App\Http\Controllers\Admin\BidController::class, 'index'])->name('bids.index');
        Route::post('bids/{bid}/void', [\App\Http\Controllers\Admin\BidController::class, 'void'])
            ->middleware('permission:manage-bids|void-bids')
            ->name('bids.void');
    });

    // ─── Vehicles Management (CRUD Granularity) ─────────────────────────
    Route::middleware('permission:manage-vehicles')->group(function () {
        Route::post('vehicles/decode-vin', [\App\Http\Controllers\Admin\VehicleController::class, 'decodeVin'])->name('vehicles.decode-vin');
        Route::get('vehicles/data', [\App\Http\Controllers\Admin\VehicleController::class, 'getData'])->name('vehicles.data');
        Route::post('vehicles/{vehicle}/approve', [\App\Http\Controllers\Admin\VehicleController::class, 'approve'])
            ->middleware('permission:manage-vehicles|approve-reject-vehicles')
            ->name('vehicles.approve');
        Route::post('vehicles/{vehicle}/reject', [\App\Http\Controllers\Admin\VehicleController::class, 'reject'])
            ->middleware('permission:manage-vehicles|approve-reject-vehicles')
            ->name('vehicles.reject');
        Route::delete('vehicles/images/{image}', [\App\Http\Controllers\Admin\VehicleController::class, 'deleteImage'])->name('vehicles.delete-image');
        Route::post('vehicles/images/{image}/set-primary', [\App\Http\Controllers\Admin\VehicleController::class, 'setPrimaryImage'])->name('vehicles.set-primary-image');
        Route::post('vehicles/images/reorder', [\App\Http\Controllers\Admin\VehicleController::class, 'reorderImages'])->name('vehicles.images.reorder');
        Route::post('vehicles/images/{image}/update', [\App\Http\Controllers\Admin\VehicleController::class, 'updateImage'])->name('vehicles.images.update');
        Route::resource('vehicles', \App\Http\Controllers\Admin\VehicleController::class);
    });
});
// Bidder Management Routes
Route::prefix('bidder')->name('bidder.')->middleware(['auth', 'role:bidder'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Bidder\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'index'])->name('bank-details.index');
    Route::post('/bank-details', [\App\Http\Controllers\Bidder\BankDetailController::class, 'update'])->name('bank-details.update');

    // Become a Seller
    Route::get('/become-seller', [\App\Http\Controllers\Bidder\SellerSubscriptionController::class, 'index'])->name('become-seller');
    Route::post('/become-seller', [\App\Http\Controllers\Bidder\SellerSubscriptionController::class, 'store'])->name('become-seller.store');

    // Seller Garage (Requires Seller Role)
    Route::prefix('garage')->name('garage.')->group(function () {
        Route::get('/listings', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'index'])->name('index');
        Route::get('/drafts', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'drafts'])->name('drafts');
        Route::get('/create', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'edit'])->name('edit');
        Route::post('/{id}/update', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'update'])->name('update');
        Route::post('/decode-vin', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'decodeVin'])->name('decode-vin');
        Route::post('/generate-description', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'generateDescription'])->name('generate-description');
        Route::post('/auto-save', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'autoSave'])->name('auto-save');
        
        // Seller Auction Management
        Route::get('/auctions', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'indexAuctions'])->name('auctions.index');
        Route::get('/auctions/{id}/show', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'showAuction'])->name('auctions.show');
        Route::get('/auctions/{id}/bids', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'auctionBids'])->name('auctions.bids');
        Route::post('/auctions/{id}/bids/{bidId}/accept', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'acceptBid'])->name('auctions.accept-bid');
        Route::post('/auctions/{id}/end', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'endEarly'])->name('auctions.end');
        Route::get('/auctions/create/{vehicle_id}', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'createAuction'])->name('auctions.create');
        Route::post('/auctions/store', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'storeAuction'])->name('auctions.store');
        Route::get('/auctions/{id}/edit', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'editAuction'])->name('auctions.edit');
        Route::post('/auctions/{id}/update', [\App\Http\Controllers\Bidder\SellerGarageController::class, 'updateAuction'])->name('auctions.update');
    });

    // Wallet Routes
    Route::get('/wallet', [\App\Http\Controllers\Bidder\WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/transactions', [\App\Http\Controllers\Bidder\WalletController::class, 'transactions'])->name('wallet.transactions');
    Route::get('/wallet/transactions/{id}/invoice', [\App\Http\Controllers\Bidder\WalletController::class, 'invoice'])->name('wallet.invoice');
    Route::post('/wallet/withdraw', [\App\Http\Controllers\Bidder\WalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    Route::post('/wallet/deposit', [\App\Http\Controllers\Bidder\WalletController::class, 'requestDeposit'])->name('wallet.deposit');
    Route::get('/wallet/payments-history', [\App\Http\Controllers\Bidder\WalletController::class, 'paymentsHistory'])->name('wallet.payments-history');
    Route::post('/wallet/hyperpay/initiate', [\App\Http\Controllers\Bidder\WalletController::class, 'initiateHyperPay'])->name('wallet.hyperpay.initiate');
    Route::get('/wallet/hyperpay/checkout/{id}', [\App\Http\Controllers\Bidder\WalletController::class, 'hyperPayCheckout'])->name('wallet.hyperpay.checkout');
    Route::get('/wallet/hyperpay/callback', [\App\Http\Controllers\Bidder\WalletController::class, 'hyperPayCallback'])->name('wallet.hyperpay.callback');
    
    // Order Payments Placeholder
    Route::get('/payments/checkout/{order}', function($order) {
        return "<h2>بوابة الدفع (HyperPay) قيد الإنشاء 🚀</h2><p>مسار الدفع للطلب رقم: {$order} سيوجهك قريباً إلى بوابة مدى/فيزا الخاصة بـ HyperPay.</p><a href='/Motorzad/public/bidder/won-auctions'>العودة للمزادات الفائزة</a>";
    })->name('payments.checkout');
    // Auctions Routes
    Route::get('/global-search', [\App\Http\Controllers\Bidder\AuctionController::class, 'globalSearch'])->name('global-search');
    Route::get('/won-auctions', [\App\Http\Controllers\Bidder\AuctionController::class, 'wonAuctions'])->name('auctions.won');
    Route::get('/my-bids', [\App\Http\Controllers\Bidder\AuctionController::class, 'myBids'])->name('auctions.my-bids');
    Route::get('/auctions', [\App\Http\Controllers\Bidder\AuctionController::class, 'index'])->name('auctions.index');
    Route::get('/auctions/{id}', [\App\Http\Controllers\Bidder\AuctionController::class, 'show'])->name('auctions.show');
    Route::post('/auctions/{id}/bid', [\App\Http\Controllers\Bidder\AuctionController::class, 'placeBid'])->name('auctions.bid');
    Route::post('/auctions/{id}/watch', [\App\Http\Controllers\Bidder\AuctionController::class, 'toggleWatchlist'])->name('auctions.watch');

    // Notifications Routes
    Route::get('/notifications/unread-state', [\App\Http\Controllers\Bidder\DashboardController::class, 'getUnreadState'])->name('notifications.unread_state');
    Route::get('/notifications', [\App\Http\Controllers\Bidder\DashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Bidder\DashboardController::class, 'markNotificationRead'])->name('notifications.read');

    // Support Tickets Routes
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Bidder\SupportController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Bidder\SupportController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Bidder\SupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [\App\Http\Controllers\Bidder\SupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [\App\Http\Controllers\Bidder\SupportController::class, 'reply'])->name('reply');
    });
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
