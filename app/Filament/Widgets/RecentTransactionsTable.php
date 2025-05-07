<?php

namespace App\Filament\Widgets;

use App\Models\PosTransaction;
use App\Models\OlshopTransaction;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactionsTable extends TableWidget
{
    protected static ?int $sort = 9;
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = '5 Transaksi Terakhir';

    protected function getTableQuery(): Builder
    {
        // Get recent transactions from both sources
        $posTransactions = PosTransaction::query()
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'trx_id' => $item->trx_id,
                    'amount' => $item->total_price,
                    'created_at' => $item->created_at,
                    'channel' => 'POS'
                ];
            });

        $olshopTransactions = OlshopTransaction::query()
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'trx_id' => $item->trx_id,
                    'amount' => $item->grand_total_amount,
                    'created_at' => $item->created_at,
                    'channel' => 'Online'
                ];
            });

        // Merge and sort
        $merged = $posTransactions->merge($olshopTransactions)
            ->sortByDesc('created_at')
            ->take(5);

        // Create a base query using one of the models
        $query = PosTransaction::query()
            ->whereIn('id', $merged->where('channel', 'POS')->pluck('id'))
            ->select([
                'id',
                'trx_id',
                'total_price as amount',
                'created_at',
                DB::raw("'POS' as channel")
            ]);

        // Add online transactions if any
        if ($merged->where('channel', 'Online')->isNotEmpty()) {
            $query->unionAll(
                OlshopTransaction::query()
                    ->whereIn('id', $merged->where('channel', 'Online')->pluck('id'))
                    ->select([
                        'id',
                        'trx_id',
                        'grand_total_amount as amount',
                        'created_at',
                        DB::raw("'Online' as channel")
                    ])
            );
        }

        // Return as Eloquent Builder
        return $query->orderBy('created_at', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('trx_id')
                ->label('ID Transaksi')
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
                }),

            TextColumn::make('amount')
                ->label('Total')
                ->money('IDR', true),

            TextColumn::make('created_at')
                ->label('Waktu')
                ->dateTime('d M Y H:i'),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10];
    }
}
