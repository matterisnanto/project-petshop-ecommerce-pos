<?php

namespace App\Filament\Resources\TransactionReturnResource\Pages;

use App\Filament\Resources\TransactionReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionReturns extends ListRecords
{
    protected static string $resource = TransactionReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
