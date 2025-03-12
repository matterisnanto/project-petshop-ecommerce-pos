<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactUsController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/products/show-all', [ProductController::class, 'showAll'])->name('products.showAll');

Route::get('/trx-check', [TrxController::class, 'index'])->name('trx');
Route::post('/trx-check', [TrxController::class, 'searchTransaction'])->name('transaction.search');

Route::get('contact-us', [ContactUsController::class, 'index'])->name('contactus');

use App\Livewire\Pos;
