<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Animals;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnimalsResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnimalsResource\RelationManagers;
use App\Models\Breeds;
use App\Models\CategoryAnimals;

class AnimalsResource extends Resource
{
    protected static ?string $model = Animals::class;

    protected static ?string $navigationLabel = 'Animals';
    protected static ?string $modelLabel = 'Animals';
    protected static ?string $pluralModelLabel = 'Animals';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 9;

    // protected static ?string $navigationIcon = 'pawprint';
    public static function getNavigationIcon(): string
    {
        return 'lucide-paw-print';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Animals::generateUniqueSlug($state));
                            })
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\hidden::make('slug'),
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->label('Animals Category')
                            ->default(null)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('description')
                                    ->maxLength(255)
                                    ->default(null),
                                Forms\Components\FileUpload::make('icon')
                                    ->image()
                                    ->columnSpan('full')
                                    ->default(null),
                            ])
                            ->columnSpan(1)
                            ->default(null),
                        Forms\Components\Select::make('breeds_id')
                            ->relationship('breeds', 'name')
                            ->label('Breeds')
                            ->default(null)
                            ->createOptionForm([
                                Forms\Components\Select::make('category_animals_id')
                                    ->relationship('categoryAnimals', 'name')
                                    ->label('Animals Category')
                                    ->default(null),
                                Forms\Components\TextInput::make('name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', Breeds::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255),
                            ])
                            ->columnSpan(1)
                            ->default(null),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('photos')
                            ->relationship('animalsPhotos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Physical Attributes')
                    ->schema([
                        Forms\Components\TextInput::make('age')
                            ->required()
                            ->numeric()
                            ->suffix('month')
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('weight')
                            ->required()
                            ->numeric()
                            ->suffix('kg')
                            ->columnSpan(1),

                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'unknown' => 'Unknown'
                            ])
                            ->required()
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Health Information')
                    ->schema([
                        Forms\Components\Select::make('health_status')
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                                'critical' => 'Critical'
                            ])
                            ->required()
                            ->columnSpan(1),
                        Forms\Components\Toggle::make('vaccination_status')
                            ->label('Vaccinated')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('selling_price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Is Acvtive')
                            ->required()
                            ->columnSpan(1),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                // ->description(fn(Animals $record): string => $record->breeds->name ?? ''),

                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('age')
                    ->sortable()
                    ->suffix(' Month')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('weight')
                    ->sortable()
                    ->suffix(' kg')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gender')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'warning',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('health_status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'critical' => 'danger',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('vaccination_status')
                    ->label('Vaccinated')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('selling_price')
                    ->label('selling price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        // Format nilai sebagai Rupiah
                        return 'Rp ' . number_format($state, 0, ',', '.');
                    }),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Available')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->relationship('categoryAnimals', 'name')
                    ->label('Category')
                    ->multiple()
                    ->preload(),

                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'unknown' => 'Unknown'
                    ]),

                Tables\Filters\SelectFilter::make('health_status')
                    ->options([
                        'excellent' => 'Excellent',
                        'good' => 'Good',
                        'fair' => 'Fair',
                        'poor' => 'Poor',
                        'critical' => 'Critical'
                    ]),

                Tables\Filters\TernaryFilter::make('vaccination_status')
                    ->label('Vaccination Status'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Availability'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                'categoryAnimal.name',
                'gender',
                'health_status',
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
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimals::route('/create'),
            // 'edit' => Pages\EditAnimals::route('/{record}/edit'),
        ];
    }
}
