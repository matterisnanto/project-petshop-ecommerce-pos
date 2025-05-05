<?php

namespace App\Filament\Resources\TransactionReturnResource\Pages;

use App\Filament\Resources\TransactionReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionReturn extends EditRecord
{
    protected static string $resource = TransactionReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
