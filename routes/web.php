<?php

use App\Livewire\Pos;
use App\Livewire\Home;
use App\Livewire\TrxCheck;

use App\Livewire\ContactUs;
use App\Livewire\ProductList;
use App\Livewire\ShoppingCart;
use App\Exports\TemplateExport;
use App\Livewire\AnimalsDetail;
use App\Livewire\AnimalsList;
use App\Livewire\Breeding;
use App\Livewire\Checkout;
use App\Livewire\OrderConfirmation;
use App\Livewire\PetGrooming;
use App\Livewire\PetHotel;
use App\Livewire\ProductDetail;
use App\Models\Animals;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::get('/', Home::class)->name('home');

Route::get('/animals', AnimalsList::class)->name('animals');
Route::get('/animals/{animals:slug}', AnimalsDetail::class)->name('animals-show');


Route::get('/products', ProductList::class)->name('products');
Route::get('/products/{product:slug}', ProductDetail::class)->name('products-show');

Route::get('/shopping-cart', ShoppingCart::class)->name('shoppingcart');

Route::get('/shopping-cart/checkout', Checkout::class)->name('checkout');

Route::get('/order-confirmation/{transaction_id}', OrderConfirmation::class)
    ->name('order-confirmation');

Route::get('/pet-grooming', PetGrooming::class)->name('pet-grooming');

Route::get('/pet-hotel', PetHotel::class)->name('pet-hotel');

Route::get('/breeding', Breeding::class)->name('breeding');

Route::get('/trx-check', TrxCheck::class)->name('trx-check');

Route::get('/contact-us', ContactUs::class)->name('contact-us');

Route::get('/debug-cart', function () {
    return response()->json([
        'cart' => session('cart'),
        'cart_totals' => session('cart_totals'),
        'checkout_data' => session('checkout_data'),
        'orderItems' => session('orderItems')
    ]);
});


Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');
