<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SupplierResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SupplierResource\RelationManagers;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Supplier';

    protected static ?string $modelLabel = 'Supplier';

    protected static ?string $navigationGroup = 'Product Resource';

    protected static ?int $navigationSort = 5;

    protected static ?string $pluralModelLabel = 'Supplier';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Supplier Information')
                    ->description('Enter complete supplier details')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Supplier Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('e.g., PT. Supplier Maju Jaya')
                                    ->columnSpan(['md' => 2]),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->unique()
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('supplier@example.com')
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->columnSpan(['md' => 2]),
                            ])
                            ->columns(2),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(255)
                            ->mask('999999999999')
                            ->prefix('+62')
                            ->stripCharacters(['-', ' '])
                            ->rule('digits_between:10,13')
                            ->afterStateHydrated(function (TextInput $component, $state) {
                                // Remove +62 if already present to avoid duplication
                                $cleaned = str_replace('+62', '', $state);
                                $component->state($cleaned);
                            })
                            ->dehydrateStateUsing(fn($state) => '+62' . $state)
                            ->placeholder('81234567890')
                            ->prefixIcon('heroicon-o-phone')
                            ->helperText('Enter number without +62 (e.g., 81234567890)')
                            ->columnSpan(['md' => 2]),

                        Forms\Components\Textarea::make('address')
                            ->label('Full Address')
                            ->required()
                            ->maxLength(255)
                            ->rows(3)
                            ->placeholder('Street, City, Province, Postal Code')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            // 'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
