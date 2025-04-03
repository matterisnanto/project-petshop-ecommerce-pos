<?php

use App\Livewire\Pos;
use App\Livewire\Home;
use App\Livewire\TrxCheck;

use App\Livewire\ContactUs;
use App\Livewire\ProductList;
use App\Livewire\ShoppingCart;
use App\Exports\TemplateExport;
use App\Livewire\Checkout;
use App\Livewire\ProductDetail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::get('/', Home::class)->name('home');

Route::get('/products', ProductList::class)->name('products');
Route::get('/products/{product:slug}', ProductDetail::class)->name('products.show');

Route::get('/shopping-cart', ShoppingCart::class)->name('shoppingcart');

Route::get('/shopping-cart/checkout', Checkout::class)->name('checkout');

Route::get('/trx-check', TrxCheck::class)->name('trx-check');

Route::get('/contact-us', ContactUs::class)->name('contact-us');



Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');
