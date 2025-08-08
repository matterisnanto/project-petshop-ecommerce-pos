<?php

use App\Livewire\Pos;
use App\Livewire\FAQs;
use App\Livewire\Home;

use App\Models\Animals;
use App\Livewire\Checkout;
use App\Livewire\PetHotel;
use App\Livewire\TrxCheck;
use App\Livewire\Breedings;
use App\Livewire\ContactUs;
use App\Livewire\AnimalsList;
use App\Livewire\PetGrooming;
use App\Livewire\ProductList;
use App\Livewire\ShoppingCart;
use App\Exports\TemplateExport;
use App\Livewire\AnimalsDetail;
use App\Livewire\ProductDetail;
use App\Livewire\PolicyandPrivacy;
use App\Livewire\OrderConfirmation;
use App\Livewire\TermsandConditions;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::get('/', Home::class)->name('home');

Route::get('/animals', AnimalsList::class)->name('animals');
Route::get('/animals/{animals:slug}', AnimalsDetail::class)->name('animals-show');


Route::get('/products', ProductList::class)->name('products');
Route::get('/products/{product:slug}', ProductDetail::class)->name('products-show');

Route::get('/shopping-cart', ShoppingCart::class)->name('shoppingcart');

Route::get('/shopping-cart/checkout', Checkout::class)->name('checkout');

Route::get('/order-confirmation/{transaction_id}', OrderConfirmation::class)->name('order-confirmation');

Route::get('/pet-grooming', PetGrooming::class)->name('pet-grooming');

Route::get('/pet-hotel', PetHotel::class)->name('pet-hotel');

Route::get('/breeding', Breedings::class)->name('breeding');

Route::get('/trx-check', TrxCheck::class)->name('trx-check');

Route::get('/contact-us', ContactUs::class)->name('contact-us');

Route::get('/faqs', FAQs::class)->name('faqs');

Route::get('/term-and-conditions', TermsandConditions::class)->name('termandconditions');

Route::get('/policy-and-privacy', PolicyandPrivacy::class)->name('policyprivacy');

Route::get('/debug-cart', function () {
    return response()->json([
        'cart' => session('cart'),
        'cart_totals' => session('cart_totals'),
        'checkout_data' => session('checkout_data')
    ]);
});

Route::get('/pos/receipt/{transaction}', [Pos::class, 'downloadReceipt'])
    ->name('pos.receipt');

Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'templateProduct.xlsx');
})->name('download-template');

Route::get('/test-komerce-api', function () {
    $apiKey = config('services.komerce.api_key');
    $keyword = 'BOJONG GEDE';

    $response = Http::withHeaders([
        'x-api-key' => $apiKey,
    ])->get('https://api-sandbox.collaborator.komerce.id/tariff/api/v1/destination/search', [
        'keyword' => $keyword
    ]);

    return [
        'status' => $response->status(),
        'response' => $response->json(),
        'api_key_used' => $apiKey ? '****' . substr($apiKey, -4) : 'null',
        'headers_sent' => ['x-api-key' => '****' . substr($apiKey, -4)]
    ];
});
