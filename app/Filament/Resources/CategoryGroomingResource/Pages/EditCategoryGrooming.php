<?php

namespace App\Filament\Resources\CategoryGroomingResource\Pages;

use App\Filament\Resources\CategoryGroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoryGrooming extends EditRecord
{
    protected static string $resource = CategoryGroomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
