<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use App\Models\Animals;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PurchaseReturn;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PurchaseReturnResource\Pages;
use App\Filament\Resources\PurchaseReturnResource\RelationManagers;

class PurchaseReturnResource extends Resource
{
    protected static ?string $model = PurchaseReturn::class;

    protected static ?string $navigationLabel = 'Purchase Return';

    protected static ?string $modelLabel = 'Purchase Return';

    protected static ?string $pluralModelLabel = 'Purchase Returns'; // Changed to plural

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
                    ->schema([
                        Forms\Components\DatePicker::make('return_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('return_number')
                            ->required()
                            ->maxLength(255)
                            ->default('PR-' . date('Ymd') . '-' . strtoupper(uniqid())),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('purchases_id')
                            ->relationship('purchases', 'purchase_number')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('returnItems', []);
                            }),
                        Forms\Components\TextInput::make('refund_amount')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\DatePicker::make('return_approved_date')
                            ->visible(fn($get) => in_array($get('status'), ['approved', 'completed'])),
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Return Items')
                    ->schema([
                        Forms\Components\Repeater::make('returnItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Hidden::make('type'), // Sembunyikan field type karena akan diisi otomatis

                                Forms\Components\Select::make('order_id')
                                    ->label('Item to Return')
                                    ->options(function ($get) {
                                        $purchaseId = $get('../../purchases_id');
                                        if (!$purchaseId) {
                                            return [];
                                        }

                                        $orders = Order::where('purchases_id', $purchaseId)
                                            ->with(['product', 'animal'])
                                            ->get();

                                        return $orders->mapWithKeys(function ($order) {
                                            if ($order->product_id) {
                                                return [$order->id => 'Product: ' . $order->product->name . ' (Qty: ' . $order->quantity . ')'];
                                            } elseif ($order->animals_id) {
                                                return [$order->id => 'Animal: ' . $order->animal->name . ' (Qty: ' . $order->quantity . ')'];
                                            }
                                            return [$order->id => 'Item #' . $order->id];
                                        });
                                    })
                                    ->required()
                                    ->columnSpan(['md' => 3])
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $order = Order::find($state);
                                        if (!$order) return;

                                        if ($order->product_id) {
                                            $set('type', 'product');
                                            $set('product_id', $order->product_id);
                                            $set('unit_price', $order->unit_price);
                                        } elseif ($order->animals_id) {
                                            $set('type', 'animal');
                                            $set('animals_id', $order->animals_id);
                                            $set('unit_price', $order->unit_price);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->maxValue(function ($get) {
                                        $orderId = $get('order_id');
                                        if (!$orderId) return null;

                                        $order = Order::find($orderId);
                                        return $order ? $order->quantity : null;
                                    }),

                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->required()
                                    ->disabled(),

                                Forms\Components\TextInput::make('reason')
                                    ->columnSpan(['md' => 2])
                                    ->required(),

                                // Hidden fields untuk menyimpan product_id atau animals_id
                                Forms\Components\Hidden::make('product_id'),
                                Forms\Components\Hidden::make('animals_id'),
                            ])
                            ->columns(3)
                            ->itemLabel(function (array $state) {
                                if (empty($state['order_id'])) {
                                    return 'New Return Item';
                                }

                                $order = Order::find($state['order_id']);
                                if (!$order) return 'Invalid Item';

                                if ($order->product_id) {
                                    return 'Product: ' . $order->product->name;
                                } elseif ($order->animals_id) {
                                    return 'Animal: ' . $order->animal->name;
                                }

                                return 'Item #' . $order->id;
                            })
                            ->defaultItems(1)
                            ->collapsible()
                            ->collapsed()
                            ->addActionLabel('Add Return Item'),
                    ]),
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
                Tables\Columns\TextColumn::make('supplier.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchases.id')
                    ->label('Purchase ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('refund_amount')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('return_approved_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('returnItems.count')
                    ->label('Items Count')
                    ->counts('returnItems'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\Filter::make('return_date')
                    ->form([
                        Forms\Components\DatePicker::make('return_from'),
                        Forms\Components\DatePicker::make('return_until'),
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
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            RelationManagers\ReturnItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseReturns::route('/'),
            'create' => Pages\CreatePurchaseReturn::route('/create'),
            'edit' => Pages\EditPurchaseReturn::route('/{record}/edit'),
            'view' => Pages\ViewPurchaseReturn::route('/{record}'),
        ];
    }
}
