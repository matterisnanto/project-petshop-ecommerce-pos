<?php

namespace App\Filament\Resources\CategoryGroomingResource\Pages;

use App\Filament\Resources\CategoryGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryGroomings extends ListRecords
{
    protected static string $resource = CategoryGroomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
