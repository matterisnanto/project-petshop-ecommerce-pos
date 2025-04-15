<?php

namespace App\Filament\Resources\CategoryAnimalsResource\Pages;

use App\Filament\Resources\CategoryAnimalsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCategoryAnimals extends ListRecords
{
    protected static string $resource = CategoryAnimalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
