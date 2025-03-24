<?php

use App\Livewire\Pos;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\shoppingcart;
use App\Http\Controllers\TrxController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\ShoppingCartController;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/products/show-all', [ProductController::class, 'showAll'])->name('products.showAll');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

Route::post('/cart/add/{id}', [ShoppingCartController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update/{id}', [ShoppingCartController::class, 'updateCart'])->name('cart.update');
Route::get('/cart/remove/{id}', [ShoppingCartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/remove-from-cart/{id}', [ShoppingCartController::class, 'removeFromCart'])->name('remove.from.cart');
Route::get('/shopping-cart', [ShoppingCartController::class, 'viewCart'])->name('cart.view');
Route::get('/shopping-cart/checkout', [ShoppingCartController::class, 'checkout'])->name('checkout');

Route::get('/trx-check', [TrxController::class, 'index'])->name('trx');
Route::post('/trx-check', [TrxController::class, 'searchTransaction'])->name('transaction.search');

Route::get('/contact-us', [ContactUsController::class, 'index'])->name('contactus');
Route::get('contact-us', [ContactUsController::class, 'index'])->name('contactus');

Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');

use App\Livewire\Pos;
