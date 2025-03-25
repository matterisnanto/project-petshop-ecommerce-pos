<?php

use App\Livewire\Pos;
use App\Livewire\Home;
use App\Exports\TemplateExport;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;


Route::get('/', Home::class);

Route::get('/download-template', function () {
    return Excel::download(new TemplateExport, 'template.xlsx');
})->name('download-template');
