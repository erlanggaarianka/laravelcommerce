<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/products', function () {
    return view('products.list');
})->name('products');

// Account Routes
Route::middleware(['auth'])->group(function () {
    // Outlet Management
    Route::prefix('outlet')->group(function () {
        Route::get('/', [App\Http\Controllers\OutletController::class, 'list'])->name('outlet.list');
        Route::get('/create', [App\Http\Controllers\OutletController::class, 'create'])->name('outlet.create');
        Route::get('/{id}', [App\Http\Controllers\OutletController::class, 'edit'])->name('outlet.edit');
    });

    // Account Management
    Route::get('/account', [App\Http\Controllers\AccountController::class, 'list'])->name('account.list');
    Route::get('/account/create', [App\Http\Controllers\AccountController::class, 'create'])->name('account.create');
    Route::get('/account/{id}', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
});
