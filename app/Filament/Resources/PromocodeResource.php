<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Promocode;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PromocodeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PromocodeResource\RelationManagers;

class PromocodeResource extends Resource
{
    protected static ?string $model = Promocode::class;

    protected static ?string $navigationLabel = 'Promo Code';

    protected static ?string $modelLabel = 'Promo Code';

    protected static ?string $pluralModelLabel = 'Promo Code';

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationGroup = 'Offers & Payments';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promo Code Information')
                    ->icon('heroicon-o-percent-badge')
                    ->description('Create a new discount promotion')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Promo Code')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. SUMMER20')
                            ->columnSpan(['md' => 1])
                            ->helperText('Enter uppercase letters and numbers only'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount Value')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->maxValue(10000000)
                            ->step(1000)
                            ->prefixIcon('heroicon-o-currency-dollar')
                            ->columnSpan(['md' => 1])
                            ->helperText('Enter the discount amount in Rupiah'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpan(['md' => 1])
                            ->helperText('When the promo becomes active'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('End Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->minDate(fn(Get $get) => $get('start_date') ?: now())
                            ->prefixIcon('heroicon-o-calendar')
                            ->columnSpan(['md' => 1])
                            ->helperText('When the promo expires'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Status')
                            ->required()
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->columnSpan(['md' => 2])
                            ->helperText('Toggle to activate/deactivate the promo'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif')
                    ->onColor('success')
                    ->offColor('danger')
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->title('Status Promo Code Diubah')
                            ->body("Promo code <strong>{$record->code}</strong> " . ($state ? 'telah diaktifkan' : 'telah dinonaktifkan'))
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListPromocodes::route('/'),
            'create' => Pages\CreatePromocode::route('/create'),
            // 'edit' => Pages\EditPromocode::route('/{record}/edit'),
        ];
    }
}
