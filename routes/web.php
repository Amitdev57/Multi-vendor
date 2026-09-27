<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\VendorController;

Route::get('/', [StorefrontController::class, 'index'])->name('home');
Route::get('/products/{product:slug}', [StorefrontController::class, 'show'])->name('products.show');

Route::get('/login', [AuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->middleware('guest')->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/items', [CartController::class, 'store'])->name('cart.store');
Route::delete('/cart/items/{product}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/checkout', [CartController::class, 'checkout'])->middleware('auth')->name('cart.checkout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/vendor/apply', [VendorController::class, 'applyForm'])->name('vendor.apply');
    Route::post('/vendor/apply', [VendorController::class, 'apply'])->name('vendor.apply.store');
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/vendor/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::get('/vendor/products', [VendorController::class, 'dashboard'])->name('vendor.products.index');
    Route::post('/vendor/products', [VendorController::class, 'storeProduct'])->name('vendor.products.store');
    Route::delete('/vendor/products/{product}', [VendorController::class, 'deleteProduct'])->name('vendor.products.destroy');
});
