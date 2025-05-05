<?php

namespace App\Filament\Resources\BreedingResource\Pages;

use App\Filament\Resources\BreedingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBreedings extends ListRecords
{
    protected static string $resource = BreedingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
