<?php

namespace App\Filament\Resources\OlshoptransactionResource\Pages;

use App\Filament\Resources\OlshoptransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOlshoptransaction extends EditRecord
{
    protected static string $resource = OlshoptransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
