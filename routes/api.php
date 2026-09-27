<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product:slug}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/checkout', [CheckoutController::class, 'store']);
    Route::post('/vendor/products', [ProductController::class, 'store'])->middleware('can:products.create');
    Route::put('/vendor/products/{product}', [ProductController::class, 'update'])->middleware('can:products.update');
    Route::delete('/vendor/products/{product}', [ProductController::class, 'destroy'])->middleware('can:products.delete');
});
