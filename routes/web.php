<?php

use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('product.index');

Route::middleware(['admin'])->group(function () {
    Route::get('/products', [App\Http\Controllers\ProductController::class, 'display'])
        ->name('product.display');
    Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])
        ->name('product.create');
    Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])
        ->name('product.store');
    Route::get('/products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])
        ->name('product.edit');
    Route::put('/products/{product}', [App\Http\Controllers\ProductController::class, 'update'])
        ->name('product.update');
    Route::delete('/products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])
        ->name('product.destroy');
    Route::get('/orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])
        ->name('order.show');
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])
        ->name('order.index');
    });

Route::post('/cart/{product}', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
Route::delete('/cart/{product}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/cart', [App\Http\Controllers\CartController::class, 'show'])->name('cart.show');

Route::post('/order', [App\Http\Controllers\OrderController::class, 'store'])->name('order.store');

