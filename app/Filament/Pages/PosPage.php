<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class PosPage extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.pos-page';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'POS Page';
    protected static ?string $modelLabel = 'POS Page';
    protected static ?string $pluralModelLabel = 'POS Page';
}
