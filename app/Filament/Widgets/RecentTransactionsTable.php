<?php

namespace App\Filament\Widgets;

use App\Models\POSTransaction;
use App\Models\OlshopTransaction;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactionsTable extends TableWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '5 Latest Transactions';

    protected function getTableQuery(): Builder
    {
        // Get recent transactions from both sources
        $posTransactions = PosTransaction::query()
            ->select([
                'id',
                'trx_id',
                'total_price as amount',
                'created_at',
                DB::raw("'POS' as channel")
            ])
            ->latest()
            ->limit(5);

        $olshopTransactions = OlshopTransaction::query()
            ->select([
                'id',
                'trx_id',
                'grand_total_amount as amount',
                'created_at',
                DB::raw("'Online' as channel")
            ])
            ->latest()
            ->limit(5);

        // Union the queries and order by created_at
        return $posTransactions->unionAll($olshopTransactions)
            ->orderBy('created_at', 'desc')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('trx_id')
                ->label('Transaction ID')
                ->searchable(),

            IconColumn::make('channel')
                ->label('Channel')
                ->icon(fn(string $state): string => match ($state) {
                    'POS' => 'heroicon-o-shopping-cart',
                    'Online' => 'heroicon-o-globe-alt',
                })
                ->color(fn(string $state): string => match ($state) {
                    'POS' => 'primary',
                    'Online' => 'success',
                })
                ->tooltip(fn(string $state): string => match ($state) {
                    'POS' => 'POS',
                    'Online' => 'Olshop',
                }),

            TextColumn::make('amount')
                ->label('Total')
                ->money('IDR', true),

            TextColumn::make('created_at')
                ->label('Time')
                ->dateTime('d M Y H:i'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10];
    }
}
