<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('product.index');

Route::post('/cart/{product}', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
Route::delete('/cart/{product}', [App\Http\Controllers\CartController::class, 'delete'])->name('cart.delete');
Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');

Route::post('/order', [App\Http\Controllers\OrderController::class, 'store'])->name('order.store');

