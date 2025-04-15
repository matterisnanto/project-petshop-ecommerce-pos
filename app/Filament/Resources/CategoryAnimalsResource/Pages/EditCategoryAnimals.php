<?php

namespace App\Filament\Resources\CategoryAnimalsResource\Pages;

use App\Filament\Resources\CategoryAnimalsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryAnimals extends EditRecord
{
    protected static string $resource = CategoryAnimalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
