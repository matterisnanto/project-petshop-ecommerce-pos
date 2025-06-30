<?php

namespace App\Filament\Resources\PetInformationResource\Pages;

use App\Filament\Resources\PetInformationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPetInformation extends ListRecords
{
    protected static string $resource = PetInformationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
