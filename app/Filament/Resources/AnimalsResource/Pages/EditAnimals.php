<?php

namespace App\Filament\Resources\AnimalsResource\Pages;

use App\Filament\Resources\AnimalsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAnimals extends EditRecord
{
    protected static string $resource = AnimalsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
