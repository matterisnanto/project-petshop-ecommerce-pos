<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetInformationResource\Pages;
use App\Filament\Resources\PetInformationResource\RelationManagers;
use App\Models\PetInformation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetInformationResource extends Resource
{
    protected static ?string $model = PetInformation::class;

    protected static ?string $navigationLabel = 'Pet Information';
    protected static ?string $modelLabel = 'Pet Information';
    protected static ?string $pluralModelLabel = 'Pet Information';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationBadgeTooltip = 'Pet On Petshop';

    public static function getNavigationIcon(): string
    {
        return 'lucide-info';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) PetInformation::where('on_petshop', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return (string) PetInformation::where('on_petshop', true)->count() > 1 ? 'warning' : 'success';
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->label('Order')
                            ->relationship('order', 'id')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Pet Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Pet Name')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(1),

                        Forms\Components\Select::make('category_animals_id')
                            ->label('Animal Category')
                            ->relationship('categoryAnimal', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('age')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->suffix('months'),

                        Forms\Components\Select::make('gender')
                            ->label('Gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'unknown' => 'Unknown',
                            ])
                            ->required(),
                    ]),

                Forms\Components\Section::make('Photo')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Pet Photo')
                            ->image()
                            ->directory('pet-photos')
                            ->maxSize(2048)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Stay Information')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('check_in')
                            ->label('Check-in Date')
                            ->required()
                            ->native(false)
                            ->minDate(now()),

                        Forms\Components\DatePicker::make('check_out')
                            ->label('Check-out Date')
                            ->required()
                            ->native(false)
                            ->minDate(fn(Forms\Get $get) => $get('check_in') ?: now()),

                        Forms\Components\TextInput::make('days')
                            ->label('Duration (days)')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        Forms\Components\Toggle::make('on_petshop')
                            ->label('Currently at Petshop?')
                            ->onColor('success')
                            ->offColor('danger')
                            ->inline(false),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Special Notes')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.postransaction.name')
                    ->label('Owner Name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Pet Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_out')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('on_petshop')
                    ->label('At Petshop')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger'),
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
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown',
                    ]),

                Tables\Filters\Filter::make('currently_at_petshop')
                    ->label('Currently at Petshop')
                    ->query(fn(Builder $query): Builder => $query->where('on_petshop', true))
                    ->toggle(),

                Tables\Filters\Filter::make('check_in_date')
                    ->form([
                        Forms\Components\DatePicker::make('check_in_from')
                            ->label('Check-in From'),
                        Forms\Components\DatePicker::make('check_in_until')
                            ->label('Check-in Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['check_in_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date),
                            )
                            ->when(
                                $data['check_in_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('check_out_date')
                    ->form([
                        Forms\Components\DatePicker::make('check_out_from')
                            ->label('Check-out From'),
                        Forms\Components\DatePicker::make('check_out_until')
                            ->label('Check-out Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['check_out_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('check_out', '>=', $date),
                            )
                            ->when(
                                $data['check_out_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('check_out', '<=', $date),
                            );
                    }),
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
            'index' => Pages\ListPetInformation::route('/'),
            'create' => Pages\CreatePetInformation::route('/create'),
            'edit' => Pages\EditPetInformation::route('/{record}/edit'),
        ];
    }
}
