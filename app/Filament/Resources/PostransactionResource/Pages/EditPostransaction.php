<?php

namespace App\Filament\Resources\PostransactionResource\Pages;

use App\Filament\Resources\PostransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPostransaction extends EditRecord
{
    protected static string $resource = PostransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
