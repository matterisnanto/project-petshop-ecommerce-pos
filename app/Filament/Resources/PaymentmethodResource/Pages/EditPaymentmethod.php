<?php

namespace App\Filament\Resources\PaymentmethodResource\Pages;

use App\Filament\Resources\PaymentmethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentmethod extends EditRecord
{
    protected static string $resource = PaymentmethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
