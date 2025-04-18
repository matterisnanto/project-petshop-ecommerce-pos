<?php

namespace App\Filament\Resources\GroomingResource\Pages;

use App\Filament\Resources\GroomingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroomings extends ListRecords
{
    protected static string $resource = GroomingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
