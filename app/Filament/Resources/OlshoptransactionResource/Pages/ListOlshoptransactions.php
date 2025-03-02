<?php

namespace App\Filament\Resources\OlshoptransactionResource\Pages;

use App\Filament\Resources\OlshoptransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOlshoptransactions extends ListRecords
{
    protected static string $resource = OlshoptransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
