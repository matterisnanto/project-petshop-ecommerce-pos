<?php

namespace App\Filament\Resources\OlshopTransactionResource\Pages;

use App\Filament\Resources\OlshopTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOlshopTransaction extends EditRecord
{
    protected static string $resource = OlshopTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
