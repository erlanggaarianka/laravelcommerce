<?php

use App\Http\Middleware\CheckUserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect(Auth::check() ? 'home' : 'login');
});

Auth::routes();

// Account Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Transaction Management
    Route::prefix('transactions')->group(function () {
        Route::get('/', [App\Http\Controllers\TransactionController::class, 'list'])->name('transactions.list');
        Route::get('/create', [App\Http\Controllers\TransactionController::class, 'create'])->name('transactions.create');
        Route::get('/receipt/{id}', [App\Http\Controllers\TransactionController::class, 'receipt'])->name('transactions.receipt');
    });

    // Inventory Management
    Route::prefix('inventory')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\InventoryController::class, 'list'])->name('inventory.list');
        Route::get('/adjust', [App\Http\Controllers\InventoryController::class, 'adjust'])->name('inventory.adjust');
    });

    // Product Management
    Route::prefix('products')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\ProductController::class, 'list'])->name('products.list');
        Route::get('/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::get('/{id}', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    });

    Route::prefix('transaction-types')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\TransactionTypeController::class, 'list'])->name('transaction-types.list');
        Route::get('/create', [App\Http\Controllers\TransactionTypeController::class, 'create'])->name('transaction-types.create');
        Route::get('/{id}', [App\Http\Controllers\TransactionTypeController::class, 'edit'])->name('transaction-types.edit');
    });

    Route::prefix('payment-methods')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\PaymentMethodController::class, 'list'])->name('payment-methods.list');
        Route::get('/create', [App\Http\Controllers\PaymentMethodController::class, 'create'])->name('payment-methods.create');
        Route::get('/{id}', [App\Http\Controllers\PaymentMethodController::class, 'edit'])->name('payment-methods.edit');
    });

    // Outlet Management
    Route::prefix('outlet')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\OutletController::class, 'list'])->name('outlet.list');
        Route::get('/create', [App\Http\Controllers\OutletController::class, 'create'])->name('outlet.create');
        Route::get('/{id}', [App\Http\Controllers\OutletController::class, 'edit'])->name('outlet.edit');
    });

    // Account Management
    Route::prefix('account')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\AccountController::class, 'list'])->name('account.list');
        Route::get('/create', [App\Http\Controllers\AccountController::class, 'create'])->name('account.create');
        Route::get('/{id}', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
    });

    // Report Management
    Route::prefix('reports')->middleware(CheckUserRole::class.':Owner')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportController::class, 'view'])->name('reports.view');
    });
});
