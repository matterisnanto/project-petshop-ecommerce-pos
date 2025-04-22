<?php

namespace App\Filament\Resources\BreedingResource\Pages;

use App\Filament\Resources\BreedingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBreeding extends EditRecord
{
    protected static string $resource = BreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
