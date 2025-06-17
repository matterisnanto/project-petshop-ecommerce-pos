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

    protected static ?int $navigationSort = 4;

    public static function getNavigationIcon(): string
    {
        return 'lucide-undo-2';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Fill in the basic details of the return')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\DatePicker::make('return_date')
                                    ->required()
                                    ->default(now())
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('return_number')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('RET-' . date('Ymd') . '-' . strtoupper(uniqid()))
                                    ->columnSpan(1)
                                    ->readOnly()
                                    ->helperText('Auto-generated return number'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'processed' => 'Processed',
                                        'refunded' => 'Refunded'
                                    ])
                                    ->required()
                                    ->default('pending')
                                    ->live()
                                    ->columnSpan(1)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if (in_array($state, ['approved', 'processed', 'refunded'])) {
                                            $set('return_approved_date', now());
                                        } else {
                                            $set('return_approved_date', null);
                                        }
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'pos' => 'POS Transaction',
                                        'olshop' => 'Online Shop Transaction',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false)
                                    ->columnSpan(1)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('pos_transaction_id', null);
                                        $set('olshop_transaction_id', null);
                                    }),

                                Forms\Components\Select::make('pos_transaction_id')
                                    ->label('POS Transaction')
                                    ->relationship('postransaction', 'trx_id')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'pos')
                                    ->columnSpan(2),

                                Forms\Components\Select::make('olshop_transaction_id')
                                    ->label('Online Shop Transaction')
                                    ->relationship('olshoptransaction', 'trx_id')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->native(false)
                                    ->visible(fn(Forms\Get $get): bool => $get('type') === 'olshop')
                                    ->columnSpan(2),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('refund_amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('return_approved_date')
                                    ->visible(fn($get) => in_array($get('status'), ['approved', 'processed', 'refunded']))
                                    ->disabled(fn($get) => $get('status') !== 'pending')
                                    ->required(fn($get) => in_array($get('status'), ['approved', 'processed', 'refunded']))
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Additional notes about this return'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Forms\Components\Section::make('Return Items')
                    ->description('Add items to be returned')
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
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('product_id', null);
                                        $set('animals_id', null);
                                        $set('grooming_id', null);
                                        $set('breeding_id', null);
                                        $set('hotel_id', null);
                                    }),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->relationship(
                                                name: 'product',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                                $get('../../type') === 'pos'
                                                    ? $query->whereHas('order', fn($q) =>
                                                    $q->where('pos_transaction_id', $get('../../pos_transaction_id')))
                                                    : $query->whereHas('order', fn($q) =>
                                                    $q->where('olshop_transaction_id', $get('../../olshop_transaction_id')))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($get) => $get('type') === 'product')
                                            ->live()
                                            ->required(fn($get) => $get('type') === 'product')
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $product = \App\Models\Product::find($state);
                                                if ($product) {
                                                    $set('unit_price', $product->price);
                                                }
                                            })
                                            ->columnSpan(2),

                                        Forms\Components\Select::make('animals_id')
                                            ->relationship(
                                                name: 'animals',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                                $get('../../type') === 'pos'
                                                    ? $query->whereHas('order', fn($q) =>
                                                    $q->where('pos_transaction_id', $get('../../pos_transaction_id')))
                                                    : $query->whereHas('order', fn($q) =>
                                                    $q->where('olshop_transaction_id', $get('../../olshop_transaction_id')))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($get) => $get('type') === 'animal')
                                            ->live()
                                            ->required(fn($get) => $get('type') === 'animal')
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $animal = \App\Models\Animals::find($state);
                                                if ($animal) {
                                                    $set('unit_price', $animal->price);
                                                }
                                            })
                                            ->columnSpan(2),

                                        // For POS-specific services
                                        Forms\Components\Select::make('grooming_id')
                                            ->relationship(
                                                name: 'grooming',
                                                titleAttribute: 'id',
                                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                                $query->whereHas('order', fn($q) =>
                                                $q->where('pos_transaction_id', $get('../../pos_transaction_id')))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($get) => $get('type') === 'grooming' && $get('../../type') === 'pos')
                                            ->live()
                                            ->required(fn($get) => $get('type') === 'grooming' && $get('../../type') === 'pos')
                                            ->columnSpan(2),

                                        Forms\Components\Select::make('breeding_id')
                                            ->relationship(
                                                name: 'breeding',
                                                titleAttribute: 'id',
                                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                                $query->whereHas('order', fn($q) =>
                                                $q->where('pos_transaction_id', $get('../../pos_transaction_id')))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($get) => $get('type') === 'breeding' && $get('../../type') === 'pos')
                                            ->live()
                                            ->required(fn($get) => $get('type') === 'breeding' && $get('../../type') === 'pos')
                                            ->columnSpan(2),

                                        Forms\Components\Select::make('hotel_id')
                                            ->relationship(
                                                name: 'hotel',
                                                titleAttribute: 'id',
                                                modifyQueryUsing: fn(Builder $query, Forms\Get $get) =>
                                                $query->whereHas('order', fn($q) =>
                                                $q->where('pos_transaction_id', $get('../../pos_transaction_id')))
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->visible(fn($get) => $get('type') === 'hotel' && $get('../../type') === 'pos')
                                            ->live()
                                            ->required(fn($get) => $get('type') === 'hotel' && $get('../../type') === 'pos')
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('unit_price')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required()
                                            ->columnSpan(1),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->required()
                                            ->default(1)
                                            ->minValue(1)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('reason')
                                            ->required()
                                            ->columnSpan(2)
                                            ->placeholder('Reason for return'),
                                    ]),
                            ])
                            ->columns(1)
                            ->itemLabel(function (array $state) {
                                if (empty($state['type'])) return 'New Return Item';

                                if ($state['type'] === 'product' && !empty($state['product_id'])) {
                                    $product = \App\Models\Product::find($state['product_id']);
                                    return $product ? "Product: {$product->name}" : 'Invalid Product';
                                }

                                if ($state['type'] === 'animal' && !empty($state['animals_id'])) {
                                    $animal = \App\Models\Animals::find($state['animals_id']);
                                    return $animal ? "Animal: {$animal->name}" : 'Invalid Animal';
                                }

                                if ($state['type'] === 'grooming' && !empty($state['grooming_id'])) {
                                    return "Grooming Service #{$state['grooming_id']}";
                                }

                                if ($state['type'] === 'breeding' && !empty($state['breeding_id'])) {
                                    return "Breeding Service #{$state['breeding_id']}";
                                }

                                if ($state['type'] === 'hotel' && !empty($state['hotel_id'])) {
                                    return "Hotel Service #{$state['hotel_id']}";
                                }

                                return 'New Return Item';
                            })
                            ->defaultItems(1)
                            ->collapsible()
                            ->collapsed()
                            ->addActionLabel('Add Item')
                            ->deleteAction(
                                fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                            )
                            ->grid(1),
                    ])
                    ->collapsible(),
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
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pos' => 'info',
                        'olshop' => 'success',
                    }),
                Tables\Columns\TextColumn::make('postransaction.trx_id')
                    ->label('POS Transaction')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('olshoptransaction.trx_id')
                    ->label('Online Transaction')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'processed' => 'primary',
                        'refunded' => 'info'
                    }),
                Tables\Columns\TextColumn::make('returnItems')
                    ->label('Items')
                    ->formatStateUsing(function (TransactionReturn $record) {
                        $count = $record->returnItems()->count();
                        $productCount = $record->returnItems()->where('type', 'product')->count();
                        $animalCount = $record->returnItems()->where('type', 'animal')->count();
                        $groomingCount = $record->returnItems()->where('type', 'grooming')->count();
                        $breedingCount = $record->returnItems()->where('type', 'breeding')->count();
                        $hotelCount = $record->returnItems()->where('type', 'hotel')->count();

                        if ($count === 0) return 'No Items';

                        $details = [];
                        if ($productCount > 0) $details[] = "{$productCount} products";
                        if ($animalCount > 0) $details[] = "{$animalCount} animals";
                        if ($groomingCount > 0) $details[] = "{$groomingCount} grooming";
                        if ($breedingCount > 0) $details[] = "{$breedingCount} breeding";
                        if ($hotelCount > 0) $details[] = "{$hotelCount} hotel";

                        return $count . ' (' . implode(', ', $details) . ')';
                    }),
                Tables\Columns\TextColumn::make('return_approved_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'pos' => 'POS Transaction',
                        'olshop' => 'Online Shop Transaction',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            // 'edit' => Pages\EditTransactionReturn::route('/{record}/edit'),
        ];
    }
}
