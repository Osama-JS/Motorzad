<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Management Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('pages/data', [\App\Http\Controllers\Admin\PageController::class, 'getData'])->name('pages.data');
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
    Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class);
    Route::get('users/data', [\App\Http\Controllers\Admin\UserController::class, 'getData'])->name('users.data');
    Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');
    Route::post('users/{user}/verify-identity', [\App\Http\Controllers\Admin\UserController::class, 'verifyIdentity'])->name('users.verify-identity');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    
    // Settings Routes
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/resources', function () {
    return view('resources');
})->name('resources');

require __DIR__.'/auth.php';
