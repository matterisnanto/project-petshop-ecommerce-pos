<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Animals;
use App\Models\Product;
use Filament\Forms\Form;
use App\Models\Purchases;
use Filament\Tables\Table;
use App\Models\PurchaseReturn;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchaseReturnResource\Pages;
use App\Filament\Resources\PurchaseReturnResource\RelationManagers;

class PurchaseReturnResource extends Resource
{
    protected static ?string $model = PurchaseReturn::class;

    protected static ?string $navigationLabel = 'Purchase Return';

    protected static ?string $modelLabel = 'Purchase Return';

    protected static ?string $pluralModelLabel = 'Purchase Returns';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Purchasing Resource';

    public static function getNavigationIcon(): string
    {
        return 'lucide-package-x';
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
                                    ->default('PR-' . date('Ymd') . '-' . strtoupper(uniqid()))
                                    ->columnSpan(1)
                                    ->readOnly()
                                    ->helperText('Auto-generated return number'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'approved' => 'Approved',
                                        'rejected' => 'Rejected',
                                        'completed' => 'Completed',
                                    ])
                                    ->required()
                                    ->default('pending')
                                    ->live()
                                    ->columnSpan(1)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        if (in_array($state, ['approved', 'completed'])) {
                                            $set('return_approved_date', now());
                                        } else {
                                            $set('return_approved_date', null);
                                        }
                                    }),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('purchases_id')
                                    ->label('Purchase Order')
                                    ->relationship('purchases', 'purchase_number')
                                    ->required()
                                    ->live()
                                    ->searchable()
                                    ->preload()
                                    ->columnSpan(2)
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $purchase = \App\Models\Purchases::find($state);
                                        $set('supplier_id', $purchase?->supplier_id);
                                        $set('supplier_name', $purchase?->supplier?->name . ' (' . $purchase?->supplier?->phone . ')' . ' - ' . $purchase?->supplier->address);
                                    }),

                                Forms\Components\TextInput::make('refund_amount')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('supplier_name')
                                    ->label('Supplier Details')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),

                                Forms\Components\DatePicker::make('return_approved_date')
                                    ->visible(fn($get) => in_array($get('status'), ['approved', 'completed']))
                                    ->disabled(fn($get) => $get('status') !== 'pending')
                                    ->required(fn($get) => in_array($get('status'), ['approved', 'completed']))
                                    ->native(false)
                                    ->displayFormat('d M Y')
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Hidden::make('supplier_id')
                            ->required(),

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
                                    ])
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $set('product_id', null);
                                        $set('animals_id', null);
                                    }),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Product')
                                            ->options(function ($get, $set) {
                                                $purchaseId = $get('../../purchases_id');
                                                if ($get('type') !== 'product' || !$purchaseId) return [];

                                                // Ambil produk dari purchase order yang dipilih
                                                return \App\Models\Order::where('purchases_id', $purchaseId)
                                                    ->whereNotNull('product_id')
                                                    ->with('product')
                                                    ->get()
                                                    ->mapWithKeys(function ($order) {
                                                        return [$order->product_id => $order->product->name];
                                                    })
                                                    ->unique();
                                            })
                                            ->visible(fn($get) => $get('type') === 'product')
                                            ->live()
                                            ->searchable()
                                            ->required(fn($get) => $get('type') === 'product')
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $product = \App\Models\Product::find($state);
                                                if ($product) {
                                                    $set('unit_price', $product->price);
                                                }
                                            })
                                            ->columnSpan(2),

                                        Forms\Components\Select::make('animals_id')
                                            ->label('Animal')
                                            ->options(function ($get, $set) {
                                                $purchaseId = $get('../../purchases_id');
                                                if ($get('type') !== 'animal' || !$purchaseId) return [];

                                                // Ambil animals dari purchase order yang dipilih
                                                return \App\Models\Order::where('purchases_id', $purchaseId)
                                                    ->whereNotNull('animals_id')
                                                    ->with('animal')
                                                    ->get()
                                                    ->mapWithKeys(function ($order) {
                                                        return [$order->animals_id => $order->animal->name];
                                                    })
                                                    ->unique();
                                            })
                                            ->visible(fn($get) => $get('type') === 'animal')
                                            ->live()
                                            ->searchable()
                                            ->required(fn($get) => $get('type') === 'animal')
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                $animal = \App\Models\Animals::find($state);
                                                if ($animal) {
                                                    $set('unit_price', $animal->price);
                                                }
                                            })
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
                                            ->maxValue(function ($get) {
                                                $purchaseId = $get('../../purchases_id');
                                                $productId = $get('product_id');
                                                $animalId = $get('animals_id');

                                                if (!$purchaseId) return null;

                                                if ($productId) {
                                                    $order = \App\Models\Order::where('purchases_id', $purchaseId)
                                                        ->where('product_id', $productId)
                                                        ->first();
                                                    return $order ? $order->quantity : null;
                                                }

                                                if ($animalId) {
                                                    $order = \App\Models\Order::where('purchases_id', $purchaseId)
                                                        ->where('animals_id', $animalId)
                                                        ->first();
                                                    return $order ? $order->quantity : null;
                                                }

                                                return null;
                                            })
                                            ->live()
                                            ->suffix(function ($get) {
                                                $purchaseId = $get('../../purchases_id');
                                                $productId = $get('product_id');
                                                $animalId = $get('animals_id');

                                                if (!$purchaseId) return null;

                                                if ($productId) {
                                                    $order = \App\Models\Order::where('purchases_id', $purchaseId)
                                                        ->where('product_id', $productId)
                                                        ->first();
                                                    return $order ? "of {$order->quantity}" : null;
                                                }

                                                if ($animalId) {
                                                    $order = \App\Models\Order::where('purchases_id', $purchaseId)
                                                        ->where('animals_id', $animalId)
                                                        ->first();
                                                    return $order ? "of {$order->quantity}" : null;
                                                }

                                                return null;
                                            }),

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

                                return 'New Return Item';
                            })
                            ->defaultItems(1)
                            ->collapsible()
                            ->collapsed()
                            ->addActionLabel('Add Item')
                            ->deleteAction(
                                fn(Forms\Components\Actions\Action $action) => $action->requiresConfirmation(),
                            )
                            ->cloneable()
                            ->grid(1),
                    ])
                    ->collapsible(),
            ]);
    }

    protected static function generateOrderLabel(Order $order): string
    {
        if ($order->product_id) {
            return "Product: {$order->product->name} (Qty: {$order->quantity})";
        } elseif ($order->animals_id) {
            return "Animal: {$order->animal->name} (Qty: {$order->quantity})";
        }
        return 'Item #' . $order->id . " (Qty: {$order->quantity})";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('return_number')
                    ->searchable()
                    ->sortable()
                    ->label('Return #')
                    ->description(fn(PurchaseReturn $record): string => $record->supplier->name ?? '')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('purchases.purchase_number')
                    ->label('Purchase Order')
                    ->searchable()
                    ->sortable()
                    ->color('primary')
                    ->icon('heroicon-o-shopping-cart'),

                Tables\Columns\TextColumn::make('return_date')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Return Date')
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        'completed' => 'heroicon-o-truck',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('refund_amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR')
                    ->label('Refund Amount')
                    ->icon('heroicon-o-currency-dollar')
                    ->color(fn(PurchaseReturn $record): string => $record->refund_amount > 0 ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->numeric()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('return_approved_date')
                    ->label('Approved Date')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar-days')
                    ->color('success')
                    ->placeholder('Not approved'),
            ])
            ->defaultSort('return_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ])
                    ->label('Status')
                    ->indicator('Status'),

                Tables\Filters\Filter::make('return_date')
                    ->form([
                        Forms\Components\DatePicker::make('return_from')
                            ->label('From Date')
                            ->native(false),
                        Forms\Components\DatePicker::make('return_until')
                            ->label('To Date')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['return_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('return_date', '>=', $date),
                            )
                            ->when(
                                $data['return_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('return_date', '<=', $date),
                            );
                    })
                    ->indicator('Date Range'),

                Tables\Filters\SelectFilter::make('supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Supplier')
                    ->indicator('Supplier'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->icon('heroicon-o-trash'),
                    Tables\Actions\BulkAction::make('markAsApproved')
                        ->label('Mark as Approved')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            $records->each->update([
                                'status' => 'approved',
                                'return_approved_date' => now(),
                            ]);
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Purchase Return'),
            ])
            ->emptyStateIcon('heroicon-o-receipt-refund')
            ->emptyStateHeading('No purchase returns yet')
            ->emptyStateDescription('Once you create your first return, it will appear here.')
            ->deferLoading()
            ->striped();
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
            'index' => Pages\ListPurchaseReturns::route('/'),
            'create' => Pages\CreatePurchaseReturn::route('/create'),
        ];
    }
}
