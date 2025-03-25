<?php

use App\Livewire\Pos;
use App\Livewire\Home;
use App\Livewire\ProductList;

use App\Exports\TemplateExport;
use App\Livewire\ContactUs;
use App\Livewire\TrxCheck;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::get('/', Home::class)->name('home');

Route::get('/products', ProductList::class)->name('products');

Route::get('/trx-check', TrxCheck::class)->name('trx-check');

Route::get('/contact-us', ContactUs::class)->name('contact-us');



Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');
