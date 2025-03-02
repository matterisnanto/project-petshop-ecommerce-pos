<?php

namespace App\Filament\Resources\PostransactionResource\Pages;

use App\Filament\Resources\PostransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPostransactions extends ListRecords
{
    protected static string $resource = PostransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
