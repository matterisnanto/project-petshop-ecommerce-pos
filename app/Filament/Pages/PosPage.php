<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class PosPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.pos-page';

    protected static ?int $navigationSort = 105;

    protected static ?string $navigationLabel = 'POS Page';
    protected static ?string $modelLabel = 'POS Page';
    protected static ?string $pluralModelLabel = 'POS Page';
}
