<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\ProductManager;
use App\Livewire\Admin\SettingManager;
use App\Livewire\Admin\StockReportManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\MemberManager;
use App\Livewire\SalesReportManager;
use App\Livewire\TransactionPointOfSale;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/members', MemberManager::class)->name('members.index');

    Route::prefix('admin')->middleware('can:isAdmin')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
        Route::get('/categories', CategoryManager::class)->name('admin.categories.index');
        Route::get('/products', ProductManager::class)->name('admin.products.index');
        Route::get('/users', UserManager::class)->name('admin.users.index');
        Route::get('/settings', SettingManager::class)->name('admin.settings.index');
        // Route::get('/pos', TransactionPointOfSale::class)->name('admin.pos.index');
        Route::get('/reports/sales', SalesReportManager::class)->name('admin.reports.sales');
        Route::get('/reports/stock', StockReportManager::class)->name('admin.reports.stock');
    });

    Route::prefix('kasir')->middleware('can:isKasir')->group(function () {
        Route::view('/dashboard', 'kasir.dashboard')->name('kasir.dashboard');
        Route::get('/reports/sales', SalesReportManager::class)->name('kasir.reports.sales');
        Route::get('/pos', TransactionPointOfSale::class)->name('kasir.pos.index');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
