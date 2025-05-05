<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionReturnResource\Pages;
use App\Filament\Resources\TransactionReturnResource\RelationManagers;
use App\Models\TransactionReturn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionReturnResource extends Resource
{
    protected static ?string $model = TransactionReturn::class;

    protected static ?string $navigationLabel = 'Transaction Return';

    protected static ?string $modelLabel = 'Transaction Return';

    protected static ?string $pluralModelLabel = 'Transaction Returns';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    public static function getNavigationIcon(): string
    {
        return 'lucide-undo-2';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Return Information')
                    ->schema([
                        Forms\Components\DatePicker::make('return_date')
                            ->required(),
                        Forms\Components\TextInput::make('return_number')
                            ->required()
                            ->maxLength(255)
                            ->default('RET-' . date('Ymd') . '-' . strtoupper(uniqid())),
                        Forms\Components\Select::make('type')
                            ->options([
                                'pos' => 'POS Transaction',
                                'olshop' => 'Online Shop Transaction',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('pos_transaction_id')
                            ->label('POS Transaction')
                            ->relationship('postransaction', 'trx_id')
                            ->searchable()
                            ->preload()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'pos'),
                        Forms\Components\Select::make('olshop_transaction_id')
                            ->label('Online Shop Transaction')
                            ->relationship('olshoptransaction', 'trx_id')
                            ->searchable()
                            ->preload()
                            ->visible(fn(Forms\Get $get): bool => $get('type') === 'olshop'),
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'completed' => 'Completed',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('return_approved_date')
                            ->visible(fn(Forms\Get $get): bool => in_array($get('status'), ['approved', 'completed'])),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Return Items')
                    ->schema([
                        Forms\Components\Repeater::make('returnItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'product' => 'Product',
                                        'animal' => 'Animal',
                                        'grooming' => 'Grooming',
                                        'breeding' => 'Breeding',
                                        'hotel' => 'Hotel',
                                    ])
                                    ->required()
                                    ->live(),
                                Forms\Components\Select::make('product_id')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'product'),
                                Forms\Components\Select::make('animals_id')
                                    ->relationship('animals', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'animal'),
                                Forms\Components\Select::make('grooming_id')
                                    ->relationship('grooming', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'grooming'),
                                Forms\Components\Select::make('breeding_id')
                                    ->relationship('breeding', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'breeding'),
                                Forms\Components\Select::make('hotel_id')
                                    ->relationship('hotel', 'id')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'hotel'),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Textarea::make('reason')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string =>
                            $state['product_id'] ?? $state['animals_id'] ?? $state['grooming_id'] ??
                                $state['breeding_id'] ?? $state['hotel_id'] ?? null)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('return_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pos_transaction_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('olshop_transaction_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('return_approved_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionReturns::route('/'),
            'create' => Pages\CreateTransactionReturn::route('/create'),
            'edit' => Pages\EditTransactionReturn::route('/{record}/edit'),
        ];
    }
}
