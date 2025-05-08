<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Models\CategoryAnimals;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryAnimalsResource\Pages;
use App\Filament\Resources\CategoryAnimalsResource\RelationManagers;

class CategoryAnimalsResource extends Resource
{
    protected static ?string $model = CategoryAnimals::class;

    protected static ?string $navigationLabel = 'Animal Category';
    protected static ?string $modelLabel = 'Animal Category';
    protected static ?string $pluralModelLabel = 'Animal Category';
    protected static ?string $navigationGroup = 'Animals Resource';
    protected static ?int $navigationSort = 11;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Provide basic details about the animal category')
                    ->icon('heroicon-o-tag') // Using a valid icon
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Category Name')
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        $set('slug', CategoryAnimals::generateUniqueSlug($state));
                                    })
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->placeholder('e.g., Dogs, Cats, Birds')
                                    ->helperText('The display name for this animal category')
                                    ->columnSpan(['md' => 2])
                                    ->prefixIcon('heroicon-o-tag'),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Identifier')
                                    ->required()
                                    ->readOnly()
                                    ->maxLength(255)
                                    ->helperText('Auto-generated from category name')
                                    ->columnSpan(['md' => 2])
                                    ->prefixIcon('heroicon-o-link'),
                            ])
                            ->columns(2),
                        
                        //Forms\Components\Divider::make()
                            //->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(255)
                            ->rows(3)
                            ->placeholder('Brief description about this animal category')
                            ->helperText('Max 255 characters')
                            ->columnSpanFull(),
                        
                        Forms\Components\FileUpload::make('icon')
                            ->label('Category Icon')
                            ->image()
                            ->directory('category-icons')
                            ->imageEditor()
                            ->imageResizeMode('contain')
                            ->imageCropAspectRatio('1:1')
                            ->panelAspectRatio('2:1')
                            ->imagePreviewHeight('150')
                            ->maxSize(512)
                            ->helperText('Recommended size: 200x200px, max 512KB')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
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
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary') // Blue color
                    ->tooltip('View'),
                    
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->tooltip('Edit'),
                    
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->tooltip('Delete'),
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
            'index' => Pages\ListCategoryAnimals::route('/'),
            'create' => Pages\CreateCategoryAnimals::route('/create'),
        ];
    }
}