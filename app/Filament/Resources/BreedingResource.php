<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use App\Models\Breeding;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BreedingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BreedingResource\RelationManagers;

class BreedingResource extends Resource
{
    protected static ?string $model = Breeding::class;

    protected static ?string $navigationLabel = 'Breeding';
    protected static ?string $modelLabel = 'Breeding';
    protected static ?string $pluralModelLabel = 'Breeding';
    protected static ?string $navigationGroup = 'Service Resource';
    protected static ?int $navigationSort = 14;

    public static function getNavigationIcon(): string
    {
        return 'lucide-baby';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Primary details about the breeding')
                    ->icon('heroicon-o-information-circle')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Breeding::generateUniqueSlug($state));
                            })
                            ->live(onBlur: true)
                            ->maxLength(100)
                            ->columnSpan(2)
                            ->label('Breeding Name')
                            ->prefixIcon('heroicon-o-tag'),
    
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Identifier')
                            ->required()
                            ->readOnly()
                            ->columnSpan(2)
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-link')
                            ->helperText('Auto-generated from breeding name'),
    
                        Forms\Components\Select::make('category_animals_id')
                            ->relationship('categoryAnimals', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Animal Category')
                            ->prefixIcon('heroicon-o-tag'),
    
                        Forms\Components\Select::make('breeds_id')
                            ->relationship('breeds', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Breed')
                            ->prefixIcon('heroicon-o-sparkles'),
    
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('breeding-photos')
                            ->imageEditor()
                            ->columnSpanFull()
                            ->label('Breeding Photo')
                            ->imagePreviewHeight('50')
                            ->panelAspectRatio('2:1')
                            ->panelLayout('integrated')
                            ->uploadingMessage('Uploading breeding photo...')
                            ->loadingIndicatorPosition('right'),
    
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpan(2)
                            ->helperText('Max 500 characters'),
                    ])->columns(2),
    
                Forms\Components\Section::make('Breeding Packages')
                    ->description('Available Breeding service packages')
                    ->icon('heroicon-o-gift')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Repeater::make('breedingPackage')
                            ->relationship('breedingPackage')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('Package name')
                                    ->columnSpan(1)
                                    ->prefixIcon('heroicon-o-cube'),
    
                                Forms\Components\TextInput::make('price')
                                ->numeric()
                                ->prefix('Rp')
                                ->minValue(0),
    
                                Forms\Components\Textarea::make('description')
                                    ->rows(2)
                                    ->maxLength(500)
                                    ->columnSpan(1)
                                    ->placeholder('Package details...'),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                            ->addActionLabel('Add Package')
                            ->defaultItems(1)
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->grid(2),
                    ]),
    
                Forms\Components\Section::make('Inventory & Pricing')
                    ->description('Stock and financial information')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->label('Available Stock')
                            ->prefixIcon('heroicon-o-archive-box'),
    
                        Forms\Components\TextInput::make('purchase_price')
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->columnSpan(['md' => 1]),
    
                        Forms\Components\TextInput::make('selling_price')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(0)
                        ->columnSpan(['md' => 1]),
    
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger')
                            ->label('Active Breeding')
                            ->onIcon('heroicon-o-check')
                            ->offIcon('heroicon-o-x-mark'),
                    ])->columns(3),
    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('')
                    ->size(40)
                    ->circular()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('categoryAnimals.name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('breeds.name')
                    ->label('Breed')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Breeding $record): string => $record->stock > 0 ? 'success' : 'danger')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->sortable()
                    ->money('IDR', locale: 'id')
                    ->color('success')
                    ->weight('bold')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_animals_id')
                    ->relationship('categoryAnimals', 'name')
                    ->label('Filter by Category')
                    ->preload()
                    ->multiple(),
                    
                Tables\Filters\SelectFilter::make('breeds_id')
                    ->relationship('breeds', 'name')
                    ->label('Filter by Breed')
                    ->preload()
                    ->multiple(),
                    
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                    
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('New Breeding'),
            ])
            ->groups([
                Tables\Grouping\Group::make('categoryAnimals.name')
                    ->label('Category')
                    ->collapsible(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListBreedings::route('/'),
            'create' => Pages\CreateBreeding::route('/create'),
            'edit' => Pages\EditBreeding::route('/{record}/edit'),
        ];
    }
}